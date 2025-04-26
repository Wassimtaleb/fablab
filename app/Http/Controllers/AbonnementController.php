<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbonnementValide;
use App\Mail\AbonnementRefuse;

class AbonnementController extends Controller
{
    private function getTypeName($type)
    {
        if ($type === 'mensuel') {
            return 'Abonnement Mensuel';
        } elseif ($type === 'annuel') {
            return 'Abonnement Annuel';
        }

        // Vérifier si c'est un type personnalisé
        $config = config('abonnements');
        if (isset($config['types'][$type])) {
            return $config['types'][$type]['name'];
        }

        return $type;
    }
    public function deleteType($id)
    {
        // Récupérer la configuration actuelle
        $config = config('abonnements');

        // Vérifier si le type existe
        if (!isset($config['types'][$id])) {
            return response()->json(['error' => 'Type d\'abonnement non trouvé'], 404);
        }

        // Supprimer le type
        unset($config['types'][$id]);

        // Sauvegarder la configuration
        $configContent = "<?php\n\nreturn " . str_replace(
            ['array (', ')', '  '], 
            ['[', ']', '    '], 
            var_export($config, true)
        ) . ";";
        file_put_contents(config_path('abonnements.php'), $configContent);

        // Vider le cache
        \Artisan::call('config:clear');

        return response()->json(['success' => true]);
    }

    public function createType()
    {
        return view('admin.abonnements.create-type');
    }

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'required|string',
            'active' => 'boolean'
        ]);

        // Récupérer la configuration actuelle
        $config = config('abonnements');

        // Générer un identifiant unique basé sur le timestamp
        $identifier = 'type_' . time();

        // Ajouter le nouveau type
        $config['types'][$identifier] = [
            'name' => $validated['name'],
            'price' => (float) $validated['price'],
            'duration' => (int) $validated['duration'],
            'description' => $validated['description'],
            'active' => $request->has('active')
        ];

        // Sauvegarder la configuration
        $configContent = "<?php\n\nreturn " . str_replace(
            ['array (', ')', '  '], 
            ['[', ']', '    '], 
            var_export($config, true)
        ) . ";";
        file_put_contents(config_path('abonnements.php'), $configContent);

        // Vider le cache
        \Artisan::call('config:clear');

        return redirect()->route('admin.abonnements.config')
            ->with('success', 'Nouveau type d\'abonnement créé avec succès');
    }
    public function showConfig()
    {
        $config = config('abonnements');
        return view('admin.abonnements.config', ['config' => $config]);
    }

    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'prix_mensuel' => 'required|numeric|min:0',
            'prix_annuel' => 'required|numeric|min:0',
            'description_mensuel' => 'nullable|string',
            'description_annuel' => 'nullable|string',
            'enable_monthly' => 'required|in:0,1',
            'enable_yearly' => 'required|in:0,1',
            'custom_types_status' => 'array'
        ]);

        // Mettre à jour le fichier de configuration
        config(['abonnements.prix_mensuel' => $validated['prix_mensuel']]);
        config(['abonnements.prix_annuel' => $validated['prix_annuel']]);
        config(['abonnements.description_mensuel' => $validated['description_mensuel']]);
        config(['abonnements.description_annuel' => $validated['description_annuel']]);
        config(['abonnements.enable_monthly' => $validated['enable_monthly'] == '1']);
        config(['abonnements.enable_yearly' => $validated['enable_yearly'] == '1']);

        // Préparer les données de configuration
        $config = [
            'prix_mensuel' => (float) $validated['prix_mensuel'],
            'prix_annuel' => (float) $validated['prix_annuel'],
            'description_mensuel' => $validated['description_mensuel'],
            'description_annuel' => $validated['description_annuel'],
            'enable_monthly' => $validated['enable_monthly'] === '1',
            'enable_yearly' => $validated['enable_yearly'] === '1',
            'types' => []
        ];

        // Récupérer les types personnalisés existants
        $existingConfig = config('abonnements');
        if (isset($existingConfig['types'])) {
            foreach ($existingConfig['types'] as $typeId => $typeData) {
                $typeData['active'] = isset($validated['custom_types_status'][$typeId]) && $validated['custom_types_status'][$typeId] === '1';
                $config['types'][$typeId] = $typeData;
            }
        }

        // Sauvegarder dans le fichier de configuration
        $configContent = "<?php\n\nreturn " . str_replace(['array (', ')', '  '], ['[', ']', '    '], var_export($config, true)) . ";";
        file_put_contents(config_path('abonnements.php'), $configContent);

        // Vider le cache de configuration
        \Artisan::call('config:clear');

        return redirect()->route('admin.abonnements.config')
            ->with('success', 'Configuration des abonnements mise à jour avec succès');
    }
    public function showForm()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/')
                           ->with('error', 'Veuillez vous connecter pour accéder au formulaire d\'abonnement.');
        }

        // Vérifier si l'utilisateur a déjà un abonnement actif
        $abonnementActif = Abonnement::where('user_id', $user->id)
                                    ->where('statut', 'actif')
                                    ->first();
        
        if ($abonnementActif) {
            return redirect()->route('user.dashboard')
                           ->with('info', 'Vous avez déjà un abonnement actif.');
        }

        // Vérifier s'il y a une demande en attente
        $demandeEnAttente = Abonnement::where('user_id', $user->id)
                                     ->where('statut', 'en_attente')
                                     ->first();

        // Récupérer la configuration des abonnements
        $config = config('abonnements');

        return view('user.subscription.form', [
            'demandeEnAttente' => $demandeEnAttente,
            'config' => $config
        ]);
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/')
                           ->with('error', 'Veuillez vous connecter pour souscrire à un abonnement.');
        }

        try {
            // Récupérer la configuration des abonnements
            $config = config('abonnements');
            
            // Valider les données du formulaire avec les types personnalisés
            $types = ['mensuel', 'annuel'];
            if (isset($config['types'])) {
                $types = array_merge($types, array_keys($config['types']));
            }
            
            $validated = $request->validate([
                'type' => 'required|in:' . implode(',', $types),
                'message' => 'nullable|string|max:500'
            ]);

            // Vérifier si une demande est déjà en attente
            $demandeExistante = Abonnement::where('user_id', $user->id)
                                         ->where('statut', 'en_attente')
                                         ->first();

            if ($demandeExistante) {
                return back()->with('error', 'Vous avez déjà une demande d\'abonnement en attente.');
            }

            // Déterminer le prix et la date de fin en fonction du type
            $prix = 0;
            $dateFin = now();
            
            if ($validated['type'] === 'mensuel') {
                $prix = $config['prix_mensuel'];
                $dateFin = now()->addMonth();
            } elseif ($validated['type'] === 'annuel') {
                $prix = $config['prix_annuel'];
                $dateFin = now()->addYear();
            } elseif (isset($config['types'][$validated['type']])) {
                $type = $config['types'][$validated['type']];
                $prix = $type['price'];
                $dateFin = now()->addDays($type['duration']);
            }

            // Créer la demande d'abonnement
            $abonnement = Abonnement::create([
                'user_id' => $user->id,
                'type' => $validated['type'],
                'message' => $validated['message'] ?? null,
                'statut' => 'en_attente',
                'date_debut' => now(),
                'date_fin' => $dateFin,
                'prix' => $prix
            ]);

            Log::info('Nouvelle demande d\'abonnement créée', [
                'abonnement_id' => $abonnement->id,
                'user_id' => $user->id
            ]);

            // Générer la facture dès la création de l'abonnement (lien de téléchargement)
            // (Optionnel : tu peux aussi envoyer un email ou autre notification ici)

            return redirect()->route('user.dashboard')
                           ->with('success', 'Votre demande d\'abonnement a été envoyée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'abonnement', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la création de votre abonnement.');
        }
    }

    public function status()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $abonnement = Abonnement::where('user_id', $user->id)
                               ->latest()
                               ->first();

        return view('user.subscription.status', compact('abonnement'));
    }

    public function liste()
    {
        $abonnements = Abonnement::with('user')
                                ->where('statut', 'en_attente')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('user.subscription.liste', compact('abonnements'));
    }

    public function validate($id)
    {
        try {
            $abonnement = Abonnement::findOrFail($id);
            
            // Mettre à jour l'abonnement
            $abonnement->update([
                'statut' => 'actif',
                'date_validation' => now()
            ]);
            
            // Récupérer l'utilisateur associé à l'abonnement
            $user = $abonnement->user;
            
            try {
                // Envoyer l'email de notification
                Mail::to($user->email)->send(new AbonnementValide($user));
                $emailSent = true;
            } catch (\Exception $e) {
                // Journaliser l'erreur d'envoi d'email mais continuer le processus
                Log::warning('Erreur lors de l\'envoi de l\'email de confirmation', [
                    'abonnement_id' => $id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                $emailSent = false;
            }
            
            // Créer un message pour l'utilisateur
            \App\Models\Message::create([
                'expediteur_type' => 'App\\Models\\Admin',
                'expediteur_id' => Auth::guard('admin')->id(),
                'destinataire_type' => 'App\\Models\\User',
                'destinataire_id' => $user->id,
                'sujet' => 'Abonnement validé',
                'contenu' => "Votre demande d'abonnement {$abonnement->type} a été validée. Votre abonnement est maintenant actif jusqu'au " . $abonnement->date_fin->format('d/m/Y') . ".",
                'lu' => false
            ]);

            Log::info('Abonnement validé', [
                'abonnement_id' => $id,
                'admin_id' => Auth::guard('admin')->id()
            ]);

            $message = 'L\'abonnement a été validé avec succès';
            if ($emailSent) {
                $message .= ' et un email de notification a été envoyé.';
            } else {
                $message .= ' mais l\'envoi de l\'email a échoué. Un message interne a été envoyé à l\'utilisateur.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation de l\'abonnement', [
                'abonnement_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // En développement, afficher plus de détails sur l'erreur
            if (config('app.debug')) {
                return back()->with('error', 'Une erreur est survenue lors de la validation de l\'abonnement: ' . $e->getMessage());
            }

            return back()->with('error', 'Une erreur est survenue lors de la validation de l\'abonnement.');
        }
    }

    public function reject($id)
    {
        try {
            $abonnement = Abonnement::findOrFail($id);
            
            // Mettre à jour l'abonnement
            $abonnement->update([
                'statut' => 'refuse',
                'date_validation' => now()
            ]);

            // Envoyer un email à l'utilisateur pour délai de paiement dépassé
            $user = $abonnement->user;
            \Mail::to($user->email)->send(new \App\Mail\AbonnementRefuse($user, $abonnement, 'Votre demande a été refusée car vous avez dépassé le délai de paiement.'));

            \Log::info('Abonnement refusé', [
                'abonnement_id' => $id,
                'admin_id' => \Auth::guard('admin')->id()
            ]);

            return back()->with('success', "L'abonnement a été refusé et l'utilisateur notifié par email.");
        } catch (\Exception $e) {
            \Log::error('Erreur lors du refus de l\'abonnement', [
                'abonnement_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Une erreur est survenue lors du refus de l\'abonnement.');
        }
    }

    public function cancel()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        try {
            $abonnement = Abonnement::where('user_id', $user->id)
                                   ->where('statut', 'actif')
                                   ->first();

            if (!$abonnement) {
                return back()->with('error', 'Aucun abonnement actif trouvé.');
            }

            $abonnement->update([
                'statut' => 'annule',
                'date_fin' => now()
            ]);

            Log::info('Abonnement annulé', ['abonnement_id' => $abonnement->id, 'user_id' => $user->id]);

            return redirect()->route('user.dashboard')
                           ->with('success', 'Votre abonnement a été annulé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation de l\'abonnement', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'annulation de votre abonnement.');
        }
    }

    public function index()
    {
        $abonnements = Abonnement::with(['user'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.abonnements.index', compact('abonnements'));
    }

    // public function edit(Abonnement $abonnement)
    // {
    //     return view('admin.abonnements.edit', compact('abonnement'));
    // }

    public function update(Request $request, Abonnement $abonnement)
    {
        // Récupérer les types d'abonnements personnalisés
        $config = config('abonnements');
        $customTypes = [];
        
        if (isset($config['types']) && is_array($config['types'])) {
            $customTypes = array_keys($config['types']);
        }
        
        // Combiner les types standard et personnalisés pour la validation
        $allowedTypes = array_merge(['mensuel', 'annuel'], $customTypes);
        
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', $allowedTypes),
            'prix' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'required|in:en_attente,actif,annule,refuse'
        ]);

        $abonnement->update($validated);

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'L\'abonnement a été mis à jour avec succès.');
    }

    public function destroy(Abonnement $abonnement)
    {
        $abonnement->delete();

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'L\'abonnement a été supprimé avec succès.');
    }
    
    /**
     * Affiche la liste des utilisateurs ayant un abonnement actif
     */
    public function activeSubscriptions()
    {
        // Récupérer tous les abonnements actifs avec les informations des utilisateurs
        $activeSubscriptions = Abonnement::with('user')
            ->where('statut', 'actif')
            ->orderBy('date_fin', 'asc')
            ->paginate(10);
            
        return view('admin.abonnements.active', compact('activeSubscriptions'));
    }
    
    /**
     * Affiche l'historique des paiements (abonnements)
     */
    public function paymentHistory()
    {
        // Récupérer tous les abonnements (qui représentent des paiements) avec les informations des utilisateurs
        $payments = Abonnement::with('user')
            ->whereIn('statut', ['actif', 'annule', 'expire'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.abonnements.payments', compact('payments'));
    }
    public function valider($id)
{
    $abonnement = Abonnement::findOrFail($id);
    $abonnement->statut = 'validé';
    $abonnement->save();

    // Envoyer une notification à l'utilisateur
    $abonnement->user->notify(new AbonnementValide());

    return redirect()->back()->with('success', 'Abonnement validé et notification envoyée.');
}
} 