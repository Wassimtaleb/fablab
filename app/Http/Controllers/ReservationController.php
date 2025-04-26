<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use App\Models\Abonnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\ReservationValidee;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index()
    {
        // Afficher toutes les réservations (pas seulement les validées)
        $reservations = Reservation::with(['user', 'machine'])
                             ->orderBy('date_reservation', 'asc')
                             ->paginate(10);

        // Ajouter le nombre d'abonnements en attente pour l'affichage dans le dashboard
        $abonnementsEnAttente = Abonnement::where('statut', 'en_attente')->count();

        return view('admin.reservations.index', compact('reservations', 'abonnementsEnAttente'));
    }

    public function create(Machine $machine)
    {
        // Vérifier si l'utilisateur a un abonnement actif
        $abonnementActif = Auth::user()->abonnements()
            ->where('statut', 'actif')
            ->first();

        if (!$abonnementActif) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Vous devez avoir un abonnement actif pour réserver une machine.');
        }

        return view('reservations.create', compact('machine'));
    }

    public function store(Request $request, Machine $machine)
    {
        try {
            // Valider les données
            $validated = $request->validate([
                'date_reservation' => 'required|date|after:today',
                'heure_debut' => 'required|date_format:H:i',
                'duree' => 'required|integer|min:1|max:6',
                'description' => 'nullable|string|max:500'
            ]);

            Log::info('Données validées', $validated);

            // Vérifier si la machine est disponible
            Log::info('Vérification de la disponibilité de la machine', [
                'machine_id' => $machine->id,
                'machine_nom' => $machine->nom,
                'disponible' => $machine->disponible,
                'statut' => $machine->statut,
                'reservations' => $machine->reservations()->where('statut', 'validée')
                    ->where('date_reservation', '>=', now())
                    ->get()
            ]);

            if (!$machine->disponible) {
                Log::warning('Machine non disponible', [
                    'machine_id' => $machine->id,
                    'machine_nom' => $machine->nom,
                    'disponible' => $machine->disponible,
                    'statut' => $machine->statut
                ]);
                return back()->with('error', 'Cette machine n\'est pas disponible pour le moment.');
            }

            // Vérifier s'il y a une maintenance planifiée ou en cours pour cette machine à cette date
            $dateReservationObj = Carbon::parse($validated['date_reservation']);
            $maintenances = \App\Models\Maintenance::where('machine_id', $machine->id)
                ->whereIn('statut', [\App\Models\Maintenance::STATUT_PLANIFIEE, \App\Models\Maintenance::STATUT_EN_COURS])
                ->get();

            foreach ($maintenances as $maintenance) {
                if ($maintenance->estEnConflitAvecReservation($dateReservationObj, $validated['heure_debut'], $validated['duree'] * 60)) {
                    Log::warning('Conflit avec une maintenance', [
                        'machine_id' => $machine->id,
                        'date_reservation' => $validated['date_reservation'],
                        'heure_debut' => $validated['heure_debut'],
                        'duree' => $validated['duree'],
                        'maintenance_id' => $maintenance->id,
                        'maintenance_date_debut' => $maintenance->date_debut,
                        'maintenance_date_fin' => $maintenance->date_fin,
                    ]);
                    return back()->with('error', 'Cette machine est en maintenance à la date et l\'heure demandées. Veuillez choisir un autre créneau.');
                }
            }

            // Convertir la durée en entier
            $duree = (int)$validated['duree'];

            // Correction : forcer la création de la date en timezone locale, sans ambiguïté
            $dateStr = $validated['date_reservation'] . ' ' . $validated['heure_debut'];
            Log::info('Date reçue du formulaire', ['date_reservation' => $validated['date_reservation']]);
            Log::info('Tentative de création de la date', ['date_string' => $dateStr]);
            try {
                $dateReservation = Carbon::createFromFormat('Y-m-d H:i', $dateStr, config('app.timezone'))->setTimezone(config('app.timezone'));
                Log::info('Date créée', ['date' => $dateReservation->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la date', [
                    'error' => $e->getMessage(),
                    'date_string' => $dateStr
                ]);
                return back()->with('error', 'La date ou l\'heure n\'est pas valide.');
            }

            // Vérifier que l'utilisateur a un abonnement actif
            $abonnementActif = Auth::user()->abonnements()
                ->where('statut', 'actif')
                ->first();

            if (!$abonnementActif) {
                return back()->with('error', 'Vous devez avoir un abonnement actif pour réserver une machine.');
            }

            // Créer la réservation avec le statut en_attente
            $reservationData = [
                'user_id' => Auth::id(),
                'machine_id' => $machine->id,
                'date_reservation' => $dateReservation->format('Y-m-d H:i:s'),
                'heure_debut' => $validated['heure_debut'],
                'duree' => $duree,
                'description' => $validated['description'] ?? null,
                'statut' => 'en_attente',
                'date_validation' => null
            ];

            // Lors de la création d'une réservation liée à un abonnement, renseigner abonnement_id
            if ($request->has('abonnement_id')) {
                $reservationData['abonnement_id'] = $request->input('abonnement_id');
            }

            Log::info('Données de réservation', $reservationData);

            try {
                $reservation = Reservation::create($reservationData);
                Log::info('Réservation créée avec succès', ['reservation_id' => $reservation->id]);

                // Créer un message pour l'administrateur
                try {
                    $admin = \App\Models\Admin::first();
                    if ($admin) {
                        \App\Models\Message::create([
                            'expediteur_type' => 'App\\Models\\User',
                            'expediteur_id' => Auth::guard('admin')->id() !== null ? Auth::guard('admin')->id() : abort(403, 'Admin non authentifié'),
                            'destinataire_type' => 'App\\Models\\Admin',
                            'destinataire_id' => $admin->id,
                            'sujet' => 'Nouvelle réservation en attente',
                            'contenu' => "Une nouvelle réservation a été créée pour la machine {$machine->nom} le {$dateReservation->format('d/m/Y')} à {$validated['heure_debut']}.",
                            'lu' => false
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Impossible de créer le message de notification', [
                        'error' => $e->getMessage()
                    ]);
                }

                return redirect()->route('reservations.my')
                    ->with('success', 'Votre demande de réservation a été envoyée. Elle sera validée par un administrateur.');

            } catch (\Exception $e) {
                Log::error('Erreur lors de la création de la réservation dans la base de données', [
                    'error' => $e->getMessage(),
                    'data' => $reservationData
                ]);
                return back()->with('error', 'Une erreur est survenue lors de la création de la réservation dans la base de données.');
            }

        } catch (\Exception $e) {
            Log::error('Erreur détaillée lors de la création de la réservation', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la création de la réservation.');
        }
    }

    public function myReservations()
    {
        $reservations = Reservation::with('machine')
            ->where('user_id', Auth::id())
            ->orderBy('date_reservation', 'desc')
            ->get();

        return view('reservations.my', compact('reservations'));
    }

    /**
     * Affiche les réservations de l'utilisateur connecté
     */
    public function userReservations()
    {
        $reservations = Reservation::with('machine')
            ->where('user_id', Auth::id())
            ->orderBy('date_reservation', 'desc')
            ->get();

        return view('user.reservations', compact('reservations'));
    }

    /**
     * Valide une réservation
     */
    public function validateReservation($id)
    {
        \Log::info('DEBUT validateReservation', ['reservation_id' => $id]);
        try {
            $reservation = Reservation::with(['user', 'machine'])->findOrFail($id);
            
            // Debug log avant update
            \Log::info('Avant validation', [
                'id' => $reservation->id,
                'date_reservation' => $reservation->date_reservation,
                'statut' => $reservation->statut
            ]);
            
            // Mettre à jour la réservation
            $reservation->statut = 'validee';
            $reservation->date_validation = now();
            $reservation->admin_id = \Auth::guard('admin')->id() !== null ? \Auth::guard('admin')->id() : abort(403, 'Admin non authentifié');
            $reservation->save();
            
            // Debug log après update
            $reservation->refresh();
            \Log::info('Après validation', [
                'id' => $reservation->id,
                'date_reservation' => $reservation->date_reservation,
                'statut' => $reservation->statut
            ]);

            // Envoyer une notification à l'utilisateur
            $user = $reservation->user;
            $machine = $reservation->machine;
            $message = "Votre réservation pour la machine {$machine->nom} le {$reservation->date_reservation->format('d/m/Y')} à {$reservation->heure_debut} a été validée.";
            
            // Créer un message pour l'utilisateur
            \App\Models\Message::create([
                'expediteur_type' => 'App\\Models\\Admin',
                'expediteur_id' => \Auth::guard('admin')->id() !== null ? \Auth::guard('admin')->id() : abort(403, 'Admin non authentifié'),
                'destinataire_type' => 'App\\Models\\User',
                'destinataire_id' => $user->id,
                'sujet' => 'Réservation validée',
                'contenu' => $message,
                'lu' => false
            ]);

            \Log::info('Préparation email réservation', [
                'user' => $reservation->user,
                'machine' => $reservation->machine,
                'date_reservation' => $reservation->date_reservation,
                'heure_debut' => $reservation->heure_debut,
                'duree' => $reservation->duree
            ]);
            // Envoi d'un email à l'utilisateur
            try {
                Mail::to($reservation->user->email)->send(new ReservationValidee($reservation));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de l\'email de validation de réservation', [
                    'reservation_id' => $reservation->id,
                    'user_email' => $reservation->user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            \Log::info('Réservation validée', [
                'reservation_id' => $id,
                'admin_id' => \Auth::guard('admin')->id()
            ]);

            return redirect()->route('admin.reservations.index')->with('success', 'La réservation a été validée avec succès. Un email de confirmation a été envoyé à l\'utilisateur.');
        } catch (\Exception $e) {
            \Log::error('Erreur globale lors de la validation de réservation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la validation de la réservation.');
        }
    }

    public function reject($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            
            // Mettre à jour la réservation
            $reservation->update([
                'statut' => 'refusee',
                'date_validation' => now()
            ]);

            // Envoyer un mail de refus à l'utilisateur
            if ($reservation->user && $reservation->user->email) {
                \Mail::to($reservation->user->email)
                    ->send(new \App\Mail\ReservationRefusee($reservation));
            }

            // Envoyer une notification à l'utilisateur
            $user = $reservation->user;
            $machine = $reservation->machine;
            $message = "Votre réservation pour la machine {$machine->nom} le {$reservation->date_reservation->format('d/m/Y')} à {$reservation->heure_debut} a été refusée.";
            
            // Créer un message pour l'utilisateur
            \App\Models\Message::create([
                'expediteur_type' => 'App\\Models\\Admin',
                'expediteur_id' => \Auth::guard('admin')->id() !== null ? \Auth::guard('admin')->id() : abort(403, 'Admin non authentifié'),
                'destinataire_type' => 'App\\Models\\User',
                'destinataire_id' => $user->id,
                'sujet' => 'Réservation refusée',
                'contenu' => $message,
                'lu' => false
            ]);

            \Log::info('Réservation refusée', [
                'reservation_id' => $id,
                'admin_id' => \Auth::guard('admin')->id()
            ]);

            return back()->with('danger', 'La réservation a été refusée avec succès. Un email d\'information a été envoyé à l\'utilisateur.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors du refus de la réservation', [
                'reservation_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors du refus de la réservation.');
        }
    }

    public function cancel(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est bien le propriétaire de la réservation
        if ($reservation->user_id !== Auth::id()) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à annuler cette réservation.');
        }

        // Vérifier que la réservation est toujours en attente
        if ($reservation->statut !== 'en_attente') {
            return back()->with('error', 'Cette réservation ne peut plus être annulée.');
        }

        $reservation->update(['statut' => 'annulee']);

        return back()->with('success', 'La réservation a été annulée avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        try {
            // Envoyer une notification à l'utilisateur
            $user = $reservation->user;
            $machine = $reservation->machine;
            $message = "Votre réservation pour la machine {$machine->nom} le {$reservation->date_reservation->format('d/m/Y')} à {$reservation->heure_debut} a été supprimée par l'administrateur.";
            \App\Models\Message::create([
                'expediteur_type' => 'App\\Models\\Admin',
                'expediteur_id' => \Auth::guard('admin')->id() !== null ? \Auth::guard('admin')->id() : abort(403, 'Admin non authentifié'),
                'destinataire_type' => 'App\\Models\\User',
                'destinataire_id' => $user->id,
                'sujet' => 'Réservation supprimée',
                'contenu' => $message,
                'lu' => false
            ]);

            // Supprimer la réservation
            $reservation->delete();

            \Log::info('Réservation supprimée', [
                'reservation_id' => $reservation->id,
                'admin_id' => \Auth::guard('admin')->id()
            ]);

            return back()->with('success', 'La réservation a été supprimée avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la réservation', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression de la réservation.');
        }
    }
    
    public function alerts(Request $request)
    {
        // Afficher directement les réservations en attente, sans filtrage
        $reservations = Reservation::with(['user', 'machine'])
            ->where('statut', 'en_attente')
            ->orderBy('date_reservation', 'asc')
            ->paginate(10);
        
        // Ajouter le nombre d'abonnements en attente pour l'affichage dans le dashboard
        $abonnementsEnAttente = Abonnement::where('statut', 'en_attente')->count();
        
        return view('admin.reservations.alerts', compact('reservations', 'abonnementsEnAttente'));
    }
    
    public function createAdmin()
    {
        // Vérifier que l'utilisateur est bien un admin
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }

        // Récupérer les utilisateurs avec leur statut d'abonnement
        $users = \App\Models\User::with(['abonnements' => function($query) {
            $query->where('statut', 'actif');
        }])->get()->map(function($user) {
            $user->has_active_subscription = $user->abonnements->isNotEmpty();
            return $user;
        });
        
        // Récupérer toutes les machines pour affichage, mais seules celles disponibles pourront être réservées
        $machines = Machine::orderBy('nom')->get();
        
        return view('admin.reservations.create', compact('users', 'machines'));
    }
    
    public function storeAdmin(Request $request)
    {
        // Vérifier que l'utilisateur est bien un admin
        if (!Auth::guard('admin')->check() || session('user_type') !== 'admin') {
            return redirect('/');
        }
        
        try {
            // Valider les données
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'machine_id' => 'required|exists:machines,id',
                'date_reservation' => 'required|date|after_or_equal:today',
                'heure_debut' => 'required|date_format:H:i',
                'duree' => 'required|integer|min:1|max:6',
                'description' => 'nullable|string|max:500',
                'statut' => 'required|in:en_attente,validee,refusee'
            ]);
            
            // Vérifier si l'utilisateur a un abonnement actif
            $user = \App\Models\User::findOrFail($validated['user_id']);
            $abonnementActif = $user->abonnements()
                ->where('statut', 'actif')
                ->first();
                
            if (!$abonnementActif) {
                return back()->with('error', 'Cet utilisateur n\'a pas d\'abonnement actif. Impossible de créer une réservation.')
                            ->withInput();
            }
            
            // Vérifier si la machine est disponible (statut)
            $machine = Machine::findOrFail($validated['machine_id']);
            if ($machine->statut !== 'disponible') {
                return back()->with('error', 'Cette machine n\'est pas disponible pour le moment.')
                            ->withInput();
            }
            
            // Créer un objet Carbon pour la date et l'heure de début
            $dateStr = $validated['date_reservation'] . ' ' . $validated['heure_debut'];
            $dateReservation = Carbon::createFromFormat('Y-m-d H:i', $dateStr, config('app.timezone'))->setTimezone(config('app.timezone'));
            
            // Créer la réservation
            $reservation = Reservation::create([
                'user_id' => $validated['user_id'],
                'machine_id' => $validated['machine_id'],
                'date_reservation' => $dateReservation->format('Y-m-d H:i:s'),
                'heure_debut' => $validated['heure_debut'],
                'duree' => $validated['duree'],
                'description' => $validated['description'],
                'statut' => $validated['statut'],
                'date_creation' => now(),
                'date_validation' => $validated['statut'] !== 'en_attente' ? now() : null
            ]);
            
            // Si la réservation est validée, envoyer une notification à l'utilisateur
            if ($validated['statut'] === 'validee') {
                $user = \App\Models\User::findOrFail($validated['user_id']);
// Ici, ajoute la logique d'envoi de message ou notification si nécessaire.
            }
            
            \Log::info('Réservation créée par admin', [
                'reservation_id' => $reservation->id,
                'admin_id' => \Auth::guard('admin')->id(),
                'user_id' => $validated['user_id'],
                'machine_id' => $validated['machine_id'],
                'statut' => $validated['statut']
            ]);
            
            return redirect()->route('admin.reservations.alerts')
                ->with('success', 'La réservation a été créée avec succès.');
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la réservation par admin', [
                'error' => $e->getMessage(),
                'admin_id' => \Auth::guard('admin')->id()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la création de la réservation: ' . $e->getMessage())
                        ->withInput();
        }
    }
    
    /**
     * Affiche le calendrier des réservations par machine
     */
    public function calendar(Request $request)
    {
        try {
            // Valider le format du mois
            $monthRegex = '/^\d{4}-\d{2}$/';
            $requestedMonth = $request->input('month');
            
            if ($requestedMonth && !preg_match($monthRegex, $requestedMonth)) {
                \Log::warning('Format de mois invalide', ['month' => $requestedMonth]);
                return redirect()->route('admin.reservations.calendar')
                    ->with('error', 'Format de date invalide. Utilisation du mois en cours.');
            }
            
            // Récupérer toutes les machines disponibles
            $machines = Machine::where('disponible', true)->orderBy('nom')->get();
            
            // Récupérer le mois sélectionné ou utiliser le mois en cours
            $selectedMonth = $requestedMonth ?: Carbon::now()->format('Y-m');
            $selectedMachineId = $request->input('machine_id');
            
            // Créer les dates de début et fin du mois avec fuseau horaire explicite
            try {
                $date = Carbon::createFromFormat('Y-m', $selectedMonth, config('app.timezone'));
                $startDate = (clone $date)->startOfMonth()->startOfDay();
                $endDate = (clone $date)->endOfMonth()->endOfDay();
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création des dates', [
                    'month' => $selectedMonth,
                    'error' => $e->getMessage()
                ]);
                return redirect()->route('admin.reservations.calendar')
                    ->with('error', 'Date invalide. Utilisation du mois en cours.');
            }
            
            \Log::info('Génération du calendrier', [
                'month' => $selectedMonth,
                'machine_id' => $selectedMachineId,
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString()
            ]);
            
            // Construire la requête pour les réservations
            $query = Reservation::with(['user', 'machine'])
                ->where('statut', 'validee')
                ->whereBetween('date_reservation', [$startDate, $endDate]);
            
            // Filtrer par machine si spécifié
            if ($selectedMachineId && $selectedMachineId != 'all') {
                $query->where('machine_id', $selectedMachineId);
            }
            
            $reservations = $query->get();
            
            // Organiser les réservations par date et par machine pour optimiser les performances
            $reservationsByDate = [];
            $reservationsByMachine = [];
            
            // Initialiser le tableau pour tous les jours du mois
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateString = $currentDate->format('Y-m-d');
                $reservationsByDate[$dateString] = [];
                $currentDate->addDay();
            }
            
            // Organiser les réservations par date et par machine
            foreach ($reservations as $reservation) {
                $dateString = $reservation->date_reservation->format('Y-m-d');
                $machineId = $reservation->machine_id;
                
                // Organiser par date
                if (!isset($reservationsByDate[$dateString])) {
                    $reservationsByDate[$dateString] = [];
                }
                $reservationsByDate[$dateString][] = $reservation;
                
                // Organiser par machine
                if (!isset($reservationsByMachine[$machineId])) {
                    $reservationsByMachine[$machineId] = [];
                }
                $reservationsByMachine[$machineId][] = $reservation;
            }
            
            // Préparer les données pour le calendrier
            $calendarData = [];
            foreach ($machines as $machine) {
                $events = [];
                
                // Utiliser les réservations déjà filtrées par machine pour éviter une double boucle
                if (isset($reservationsByMachine[$machine->id])) {
                    foreach ($reservationsByMachine[$machine->id] as $reservation) {
                        try {
                            // Calculer l'heure de fin basée sur la durée (en minutes)
                            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $reservation->date_reservation->format('Y-m-d') . ' ' . $reservation->heure_debut, config('app.timezone'))->setTimezone(config('app.timezone'));
                            $endDateTime = (clone $startDateTime)->addMinutes($reservation->duree);
                            
                            $events[] = [
                                'id' => $reservation->id,
                                'title' => $reservation->user->nom . ' ' . $reservation->user->prenom,
                                'start' => $startDateTime->format('Y-m-d\TH:i:s'), // pas de Z, heure locale
                                'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                                'description' => $reservation->description ?: 'Pas de description'
                            ];
                        } catch (\Exception $e) {
                            \Log::error('Erreur lors du traitement de la réservation', [
                                'reservation_id' => $reservation->id,
                                'error' => $e->getMessage()
                            ]);
                            // Continuer avec les autres réservations
                            continue;
                        }
                    }
                }
                
                $calendarData[$machine->id] = [
                    'machine' => $machine,
                    'events' => $events
                ];
            }
            
            // Préparer les données pour la navigation entre les mois
            $prevMonth = (clone $date)->subMonth()->format('Y-m');
            $nextMonth = (clone $date)->addMonth()->format('Y-m');
            $currentMonth = $date->locale('fr')->format('F Y'); // Nom du mois et année en français
            
            return view('admin.reservations.calendar', compact(
                'calendarData', 
                'machines', 
                'reservationsByDate',
                'selectedMonth',
                'selectedMachineId',
                'prevMonth',
                'nextMonth',
                'currentMonth'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du calendrier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Une erreur est survenue lors de la génération du calendrier. Veuillez réessayer.');
        }
    }
    
    /**
     * Récupère les détails d'une réservation au format JSON
     */
    public function getDetails($id)
    {
        try {
            $reservation = Reservation::with(['user', 'machine'])
                ->where('id', $id)
                ->where('statut', 'validee')
                ->firstOrFail();
            
            return response()->json([
                'id' => $reservation->id,
                'user' => [
                    'id' => $reservation->user->id,
                    'nom' => $reservation->user->nom,
                    'prenom' => $reservation->user->prenom,
                    'email' => $reservation->user->email
                ],
                'machine' => [
                    'id' => $reservation->machine->id,
                    'nom' => $reservation->machine->nom
                ],
                'date_reservation' => $reservation->date_reservation->format('Y-m-d'),
                'heure_debut' => $reservation->heure_debut,
                'duree' => $reservation->duree,
                'description' => $reservation->description,
                'statut' => $reservation->statut,
                'date_validation' => $reservation->date_validation ? $reservation->date_validation->format('Y-m-d H:i:s') : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des détails de la réservation', [
                'reservation_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Réservation non trouvée'], 404);
        }
    }

    /**
     * Return all validated reservations for a specific date (AJAX for calendar)
     */
    public function getByDate($date)
    {
        $reservations = \App\Models\Reservation::with(['user', 'machine'])
            ->whereDate('date_reservation', $date)
            ->where('statut', 'validee')
            ->orderBy('heure_debut')
            ->get();
        return response()->json($reservations);
    }
}
