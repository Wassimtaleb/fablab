<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin; // Utilisez le modèle Admin
use App\Models\User;
use App\Models\Technicien;
use Illuminate\Validation\Rule;
use App\Models\Abonnement;
use App\Models\Reservation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{

    // Afficher le formulaire d'inscription
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    // Gérer l'inscription
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
        ]);

        $admin = Admin::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Régénérer la session
        $request->session()->regenerate();
        
        // Connecter l'admin
        Auth::guard('admin')->login($admin);
        
        // Définir le type d'utilisateur
        $request->session()->put('user_type', 'admin');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Compte administrateur créé avec succès. Vous êtes maintenant connecté.');
    }

    // Afficher le formulaire de connexion admin
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Traiter la connexion admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('user_type', 'admin');
            return redirect()->route('admin.dashboard')->with('success', 'Connexion réussie.');
        }
        return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
    }

    // Afficher le tableau de bord de l'admin
    public function dashboard()
    {
        // Récupérer l'admin connecté
        $admin = Auth::guard('admin')->user();
        
        // Statistiques pour le tableau de bord
        $stats = [
            'users' => User::count(),
            'machines' => \App\Models\Machine::count(),
            'reservations' => Reservation::count(),
            'reservations_validees' => Reservation::where('statut', 'validee')->count(),
            'reservations_en_attente' => Reservation::where('statut', 'en_attente')->count(),
            'abonnements' => Abonnement::count(),
            'abonnements_actifs' => Abonnement::where('statut', 'actif')->count(),
            'abonnements_en_attente' => Abonnement::where('statut', 'en_attente')->count()
        ];

        // Réservations par mois (12 derniers mois)
        $reservationsParMois = \App\Models\Reservation::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        // Nouveaux utilisateurs par mois
        $usersParMois = \App\Models\User::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        // Top 3 machines les plus utilisées
        $topMachines = \App\Models\Reservation::selectRaw('machine_id, COUNT(*) as total')
            ->groupBy('machine_id')
            ->orderByDesc('total')
            ->take(3)
            ->with('machine')
            ->get();

        return view('admin.dashboard', compact('admin', 'stats', 'reservationsParMois', 'usersParMois', 'topMachines'));
    }

    // Afficher le formulaire de suppression d'un admin
    public function delete()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $admins = Admin::all();
        return view('admin.delete', compact('admins'));
    }

    // Supprimer un admin
    public function destroy(Request $request)
    {
        // Vérifier l'authentification
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        // Valider la requête
        $request->validate([
            'email' => 'required|email',
        ]);
    
        // Trouver l'admin par son email
        $admin = Admin::where('email', $request->email)->first();
    
        // Vérifier si l'admin existe
        if (!$admin) {
            return back()->withErrors(['email' => 'Admin non trouvé']);
        }
    
        // Récupérer l'admin connecté
        $currentAdmin = Auth::guard('admin')->user();
        
        // Vérifier si l'admin connecté existe
        if (!$currentAdmin) {
            return back()->withErrors(['email' => 'Vous devez être connecté en tant qu\'administrateur']);
        }
    
        // Empêcher l'admin de se supprimer lui-même
        if ($admin->email === $currentAdmin->email) {
            return back()->withErrors(['email' => 'Vous ne pouvez pas vous supprimer vous-même']);
        }
    
        // Supprimer l'admin
        $admin->delete();
        
        Log::info('Administrateur supprimé', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'deleted_by' => $currentAdmin->id
        ]);
        
        return redirect()->route('admin.dashboard')->with('success', 'Admin supprimé avec succès');
    }

    // Afficher le formulaire de création d'un nouvel admin
    public function create()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        return view('admin.create');
    }

    // Stocker un nouvel admin dans la base de données
    public function store(Request $request)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:admins', // Vérifier l'unicité dans la table admins
            'password' => 'required|min:8',
        ]);

        Admin::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash du mot de passe
        ]);
        
        Log::info('Nouvel administrateur créé', [
            'admin_email' => $request->email,
            'created_by' => Auth::guard('admin')->id()
        ]);
        
        return redirect()->route('admin.dashboard')->with('success', 'Admin créé avec succès');
    }

    // Afficher le formulaire de création d'un utilisateur
    public function createUser()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        return view('admin.create_user');
    }

    // Stocker un nouvel utilisateur dans la base de données
    public function storeUser(Request $request)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
        ]);
        
        Log::info('Nouvel utilisateur créé par administrateur', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'created_by' => Auth::guard('admin')->id()
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur créé avec succès');
    }


    ////////
    // Afficher le formulaire de création d'un TECHNICIEN
    public function createTech()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        return view('admin.create_tech');
    }

    // Stocker un nouvel TECHNICIEN dans la base de données
    public function storeTech(Request $request)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:techniciens',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $technicien = Technicien::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        Log::info('Nouveau technicien créé par administrateur', [
            'technicien_id' => $technicien->id,
            'technicien_email' => $technicien->email,
            'created_by' => Auth::guard('admin')->id()
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Technicien créé avec succès');
    }

    // Afficher le formulaire de suppression d'un utilisateur
    public function deleteUser()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $users = User::all();
        return view('admin.delete_user', compact('users'));
    }

    // Supprimer un utilisateur
    public function destroyUser(Request $request)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur non trouvé']);
        }

        // Vérifier si l'administrateur essaie de supprimer un compte utilisateur lié à son email
        $admin = Auth::guard('admin')->user();
        if ($admin && $user->email === $admin->email) {
            return back()->withErrors(['email' => 'Vous ne pouvez pas supprimer un compte utilisateur associé à votre email d\'administrateur']);
        }

        try {
            // Commencer une transaction pour s'assurer que tout est supprimé ou rien
            \DB::beginTransaction();
            
            // 1. Supprimer les réservations liées à l'utilisateur
            Reservation::where('user_id', $user->id)->delete();
            
            // 2. Supprimer les abonnements liées à l'utilisateur
            Abonnement::where('user_id', $user->id)->delete();
            
            // 3. Supprimer les messages où l'utilisateur est expéditeur ou destinataire
            Message::where(function($query) use ($user) {
                $query->where('expediteur_id', $user->id)
                      ->where('expediteur_type', 'App\\Models\\User');
            })->orWhere(function($query) use ($user) {
                $query->where('destinataire_id', $user->id)
                      ->where('destinataire_type', 'App\\Models\\User');
            })->delete();
            
            // 4. Supprimer l'utilisateur lui-même
            $user->delete();
            
            // Valider la transaction
            \DB::commit();
            
            Log::info('Utilisateur supprimé avec succès', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => $admin->id
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Utilisateur et toutes ses données associées supprimés avec succès');
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler toutes les modifications
            \DB::rollBack();
            
            Log::error('Erreur lors de la suppression de l\'utilisateur', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['email' => 'Une erreur est survenue lors de la suppression de l\'utilisateur: ' . $e->getMessage()]);
        }
    }

    // Afficher le formulaire de suppression d'un technicien
    public function deleteTech()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $techniciens = Technicien::all();
        return view('admin.delete_tech', compact('techniciens'));
    }

    // Supprimer un technicien
    public function destroyTech(Request $request)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $technicien = Technicien::where('email', $request->email)->first();

        if (!$technicien) {
            return back()->withErrors(['email' => 'Technicien non trouvé']);
        }

        // Vérifier si l'administrateur essaie de supprimer un compte technicien lié à son email
        $admin = Auth::guard('admin')->user();
        if ($admin && $technicien->email === $admin->email) {
            return back()->withErrors(['email' => 'Vous ne pouvez pas supprimer un compte technicien associé à votre email d\'administrateur']);
        }

        try {
            // Commencer une transaction
            \DB::beginTransaction();
            
            // Supprimer les messages où le technicien est expéditeur ou destinataire
            Message::where(function($query) use ($technicien) {
                $query->where('expediteur_id', $technicien->id)
                      ->where('expediteur_type', 'App\\Models\\Technicien');
            })->orWhere(function($query) use ($technicien) {
                $query->where('destinataire_id', $technicien->id)
                      ->where('destinataire_type', 'App\\Models\\Technicien');
            })->delete();
            
            // Supprimer le technicien
            $technicien->delete();
            
            // Valider la transaction
            \DB::commit();
            
            Log::info('Technicien supprimé avec succès', [
                'technicien_id' => $technicien->id,
                'technicien_email' => $technicien->email,
                'admin_id' => $admin->id
            ]);
            
            return redirect()->route('admin.dashboard')->with('success', 'Technicien supprimé avec succès');
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler toutes les modifications
            \DB::rollBack();
            
            Log::error('Erreur lors de la suppression du technicien', [
                'technicien_id' => $technicien->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['email' => 'Une erreur est survenue lors de la suppression du technicien: ' . $e->getMessage()]);
        }
    }

    // Afficher la page des statistiques d'utilisation
    public function statistiques()
    {
        $admin = Auth::guard('admin')->user();
        $stats = [
            'users' => User::count(),
            'machines' => \App\Models\Machine::count(),
            'reservations' => Reservation::count(),
            'reservations_validees' => Reservation::where('statut', 'validee')->count(),
            'abonnements' => Abonnement::count(),
            'abonnements_actifs' => Abonnement::where('statut', 'actif')->count(),
            'abonnements_en_attente' => Abonnement::where('statut', 'en_attente')->count()
        ];
        $moisNoms = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];

        // Réservations par mois (12 mois, 0 si aucun)
        $reservationsRaw = \App\Models\Reservation::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->startOfYear())
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at) ASC')
            ->pluck('total', 'mois');
        $reservationsParMois = [];
        foreach ($moisNoms as $num => $nom) {
            $reservationsParMois[] = $reservationsRaw[$num] ?? 0;
        }
        // Nouveaux utilisateurs par mois (12 mois, 0 si aucun)
        $usersRaw = \App\Models\User::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->startOfYear())
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at) ASC')
            ->pluck('total', 'mois');
        $usersParMois = [];
        foreach ($moisNoms as $num => $nom) {
            $usersParMois[] = $usersRaw[$num] ?? 0;
        }
        // Abonnements créés par mois (12 mois, 0 si aucun)
        $abonnementsRaw = \App\Models\Abonnement::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->startOfYear())
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at) ASC')
            ->pluck('total', 'mois');
        $abonnementsParMois = [];
        foreach ($moisNoms as $num => $nom) {
            $abonnementsParMois[] = $abonnementsRaw[$num] ?? 0;
        }
        $topMachines = \App\Models\Reservation::selectRaw('machine_id, COUNT(*) as total')
            ->groupBy('machine_id')
            ->orderByDesc('total')
            ->take(3)
            ->with('machine')
            ->get();
        return view('admin.statistiques', compact('admin', 'stats', 'reservationsParMois', 'usersParMois', 'abonnementsParMois', 'topMachines'));
    }

    // Afficher le formulaire de modification d'un admin
    public function edit()
    {
        $admins = Admin::all();
        return view('admin.edit', compact('admins'));
    }

    // Mettre à jour un admin
    public function update(Request $request, Admin $admin)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $admin->nom = $request->nom;
        $admin->prenom = $request->prenom;
        $admin->email = $request->email;
        
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        
        $admin->save();

        return redirect()->route('admin.dashboard')->with('success', 'Admin mis à jour avec succès');
    }

    // Afficher le formulaire de modification d'un utilisateur
    public function editUser()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $users = User::all();
        return view('admin.edit_user', compact('users'));
    }

    // Mettre à jour un utilisateur
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur mis à jour avec succès');
    }

    // Afficher le formulaire de modification d'un technicien
    public function editTech()
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        $techniciens = Technicien::all();
        return view('admin.edit_tech', compact('techniciens'));
    }

    // Mettre à jour un technicien
    public function updateTech(Request $request, Technicien $technicien)
    {
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('techniciens')->ignore($technicien->id)],
            'password' => 'nullable|string|min:8',
        ]);

        $technicien->nom = $request->nom;
        $technicien->prenom = $request->prenom;
        $technicien->email = $request->email;
        
        if ($request->filled('password')) {
            $technicien->password = Hash::make($request->password);
        }
        
        $technicien->save();

        return redirect()->route('admin.dashboard')->with('success', 'Technicien mis à jour avec succès');
    }

    // Utilitaire : mapping des types d'abonnement (adapter selon ta logique réelle)
    private function getTypesMap()
    {
        return [
            'normal' => 'Normal',
            'premium' => 'Premium',
            'etudiant' => 'Étudiant',
            'semi-mensuel' => 'Semi-mensuel',
            'type_1744424501' => 'Semi-mensuel',
            // Ajoute ici tous les types utilisés dans ta base
        ];
    }

    // Afficher le rapport financier
    public function finances() {
        // Utiliser le statut 'actif' pour les abonnements validés
        $abonnements = \App\Models\Abonnement::where('statut', 'actif');
        // Paiements reçus par mois ET par type
        $paiementsParMois = $abonnements
            ->selectRaw('YEAR(date_validation) as annee, MONTH(date_validation) as mois, type, SUM(prix) as total')
            ->whereNotNull('date_validation')
            ->where('date_validation', '>=', now()->subYear())
            ->groupByRaw('YEAR(date_validation), MONTH(date_validation), type')
            ->orderByRaw('YEAR(date_validation) ASC, MONTH(date_validation) ASC, type ASC')
            ->get();
        // Total des recettes
        $totalRecettes = (clone $abonnements)->whereNotNull('date_validation')->sum('prix');
        // Statistiques par type d’abonnement (par le champ "type")
        $statsType = \App\Models\Abonnement::where('statut', 'actif')
            ->whereNotNull('date_validation')
            ->selectRaw('type, COUNT(*) as count, SUM(prix) as total')
            ->groupBy('type')
            ->get();
        return view('admin.finances', compact('paiementsParMois', 'totalRecettes', 'statsType'));
    }

    // EXPORT CSV : Abonnements
    public function exportAbonnementsCsv()
    {
        $abonnements = \App\Models\Abonnement::with('user')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="abonnements.csv"',
        ];
        $callback = function() use ($abonnements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Utilisateur', 'Email', 'Type', 'Prix', 'Statut', 'Date début', 'Date fin', 'Date création']);
            foreach ($abonnements as $abonnement) {
                fputcsv($handle, [
                    $abonnement->user ? ($abonnement->user->nom . ' ' . $abonnement->user->prenom) : '',
                    $abonnement->user ? $abonnement->user->email : '',
                    $this->getTypesMap()[$abonnement->type] ?? $abonnement->type,
                    $abonnement->prix,
                    ucfirst($abonnement->statut),
                    $abonnement->date_debut,
                    $abonnement->date_fin,
                    $abonnement->created_at,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT CSV : Paiements
    public function exportPaiementsCsv()
    {
        $paiements = \App\Models\Abonnement::whereNotNull('date_validation')->with('user')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="paiements.csv"',
        ];
        $callback = function() use ($paiements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Utilisateur', 'Type', 'Montant', 'Date paiement']);
            foreach ($paiements as $paiement) {
                fputcsv($handle, [
                    $paiement->user ? ($paiement->user->nom . ' ' . $paiement->user->prenom) : '',
                    $this->getTypesMap()[$paiement->type] ?? $paiement->type,
                    $paiement->prix,
                    $paiement->date_validation,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT CSV : Réservations
    public function exportReservationsCsv()
    {
        $reservations = \App\Models\Reservation::with(['user', 'machine'])->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reservations.csv"',
        ];
        $callback = function() use ($reservations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Utilisateur', 'Machine', 'Date début', 'Date fin', 'Statut']);
            foreach ($reservations as $reservation) {
                fputcsv($handle, [
                    $reservation->user ? ($reservation->user->nom . ' ' . $reservation->user->prenom) : '',
                    $reservation->machine ? $reservation->machine->nom : '',
                    $reservation->date_debut,
                    $reservation->date_fin,
                    ucfirst($reservation->statut),
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT CSV : Machines
    public function exportMachinesCsv()
    {
        $machines = \App\Models\Machine::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="machines.csv"',
        ];
        $callback = function() use ($machines) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nom', 'Catégorie', 'Statut', 'Caractéristiques']);
            foreach ($machines as $machine) {
                fputcsv($handle, [
                    $machine->nom,
                    $machine->categorie,
                    ucfirst($machine->statut),
                    $machine->caracteristiques,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT CSV : Statistiques d'utilisation
    public function exportStatistiquesCsv()
    {
        $stats = \App\Models\Reservation::selectRaw('machine_id, COUNT(*) as nb_reservations')
            ->groupBy('machine_id')
            ->with('machine')
            ->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="statistiques.csv"',
        ];
        $callback = function() use ($stats) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Machine', 'Nombre de réservations']);
            foreach ($stats as $stat) {
                fputcsv($handle, [
                    $stat->machine ? $stat->machine->nom : '',
                    $stat->nb_reservations,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT CSV : Rapport financier
    public function exportFinancesCsv()
    {
        $finances = \App\Models\Abonnement::where('statut', 'actif')
            ->whereNotNull('date_validation')
            ->selectRaw('type, SUM(prix) as total')
            ->groupBy('type')
            ->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="finances.csv"',
        ];
        $callback = function() use ($finances) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Type', 'Total recettes']);
            foreach ($finances as $finance) {
                fputcsv($handle, [
                    $this->getTypesMap()[$finance->type] ?? $finance->type,
                    $finance->total,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // EXPORT PDF : Tout en un seul fichier (multi-sections)
    public function exportToutPdf()
    {
        $abonnements = \App\Models\Abonnement::with('user')->get();
        $paiements = \App\Models\Abonnement::whereNotNull('date_validation')->with('user')->get();
        $reservations = \App\Models\Reservation::with(['user', 'machine'])->get();
        $machines = \App\Models\Machine::all();
        $statistiques = \App\Models\Reservation::selectRaw('machine_id, COUNT(*) as nb_reservations')->groupBy('machine_id')->with('machine')->get();
        $finances = \App\Models\Abonnement::where('statut', 'actif')->whereNotNull('date_validation')->selectRaw('type, SUM(prix) as total')->groupBy('type')->get();
        // Types abonnements pour affichage humain
        $types = $this->getTypesMap();
        $pdf = Pdf::loadView('admin.export_tout_pdf', compact('abonnements', 'paiements', 'reservations', 'machines', 'statistiques', 'finances', 'types'));
        return $pdf->download('export_fablab_tout.pdf');
    }
}