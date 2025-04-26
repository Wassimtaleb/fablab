<?php

namespace App\Http\Controllers\Technicien;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceComment;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Affiche la liste des maintenances pour le technicien connecté
     */
    public function index()
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Récupérer les maintenances du technicien
        $maintenancesEnCours = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', Maintenance::STATUT_EN_COURS)
            ->with('machine')
            ->orderBy('date_debut', 'desc')
            ->get();
            
        $maintenancesPlanifiees = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', Maintenance::STATUT_PLANIFIEE)
            ->with('machine')
            ->orderBy('date_debut', 'asc')
            ->get();
            
        $maintenancesTerminees = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', Maintenance::STATUT_TERMINEE)
            ->with('machine')
            ->orderBy('date_debut', 'desc')
            ->get();
        
        return view('technicien.maintenances.index', compact(
            'maintenancesEnCours', 
            'maintenancesPlanifiees', 
            'maintenancesTerminees'
        ));
    }
    
    /**
     * Affiche les détails d'une maintenance
     */
    public function show(Maintenance $maintenance)
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Vérifier que la maintenance appartient au technicien connecté
        if ($maintenance->technicien_id !== $technicien->id) {
            return redirect()->route('technicien.maintenances.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette maintenance.');
        }
        
        // Récupérer les commentaires de la maintenance
        $commentaires = $maintenance->commentaires()->with('technicien')->orderBy('created_at', 'desc')->get();
        
        return view('technicien.maintenances.show', compact('maintenance', 'commentaires'));
    }
    
    /**
     * Met à jour le statut d'une maintenance
     */
    public function updateStatus(Request $request, Maintenance $maintenance)
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Vérifier que la maintenance appartient au technicien connecté
        if ($maintenance->technicien_id !== $technicien->id) {
            return redirect()->route('technicien.maintenances.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette maintenance.');
        }
        
        $request->validate([
            'statut' => 'required|in:' . implode(',', [
                Maintenance::STATUT_PLANIFIEE,
                Maintenance::STATUT_EN_COURS,
                Maintenance::STATUT_TERMINEE,
                Maintenance::STATUT_ANNULEE
            ]),
            'notes' => 'nullable|string',
        ]);
        
        // Mettre à jour le statut et les notes
        $maintenance->statut = $request->statut;
        
        // Si des notes sont fournies, les ajouter
        if ($request->filled('notes')) {
            $maintenance->notes = $request->notes;
        }
        
        // Si le statut est "terminée", définir la date de fin
        if ($request->statut === Maintenance::STATUT_TERMINEE && !$maintenance->date_fin) {
            $maintenance->date_fin = now();
        }
        
        $maintenance->save();
        
        // Ajouter un commentaire automatique pour le changement de statut
        $statusText = [
            Maintenance::STATUT_PLANIFIEE => 'planifiée',
            Maintenance::STATUT_EN_COURS => 'en cours',
            Maintenance::STATUT_TERMINEE => 'terminée',
            Maintenance::STATUT_ANNULEE => 'annulée'
        ][$request->statut];
        
        MaintenanceComment::create([
            'maintenance_id' => $maintenance->id,
            'technicien_id' => $technicien->id,
            'contenu' => "Statut changé à : $statusText"
        ]);
        
        return redirect()->route('technicien.maintenances.show', $maintenance)
            ->with('success', 'Statut de la maintenance mis à jour avec succès.');
    }
    
    /**
     * Ajoute un commentaire à une maintenance
     */
    public function addComment(Request $request, Maintenance $maintenance)
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Vérifier que la maintenance appartient au technicien connecté
        if ($maintenance->technicien_id !== $technicien->id) {
            return redirect()->route('technicien.maintenances.index')
                ->with('error', 'Vous n\'êtes pas autorisé à commenter cette maintenance.');
        }
        
        $request->validate([
            'contenu' => 'required|string|min:3',
        ]);
        
        // Créer le commentaire
        MaintenanceComment::create([
            'maintenance_id' => $maintenance->id,
            'technicien_id' => $technicien->id,
            'contenu' => $request->contenu
        ]);
        
        return redirect()->route('technicien.maintenances.show', $maintenance)
            ->with('success', 'Commentaire ajouté avec succès.');
    }
    
    /**
     * Affiche le formulaire pour créer une nouvelle maintenance
     */
    public function create()
    {
        // Récupérer toutes les machines sans filtrer par statut
        $machines = Machine::all();
        
        return view('technicien.maintenances.create', compact('machines'));
    }
    
    /**
     * Enregistre une nouvelle maintenance
     */
    public function store(Request $request)
    {
        $technicien = Auth::guard('technicien')->user();
        
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'date_debut' => 'required|date|after_or_equal:today',
            'duree' => 'required|integer|min:15|max:480', // Durée en minutes (min 15 min, max 8h)
        ]);
        
        // Calculer la date de fin à partir de la durée
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = (clone $dateDebut)->addMinutes((int) $request->duree);
        
        // Vérifier s'il y a des réservations pour cette machine pendant cette période
        $reservationsConflits = \App\Models\Reservation::where('machine_id', $request->machine_id)
            ->where('statut', 'validee')
            ->where('date_reservation', $dateDebut->format('Y-m-d'))
            ->get()
            ->filter(function($reservation) use ($dateDebut, $dateFin) {
                $heureDebut = \Carbon\Carbon::createFromFormat('H:i', $reservation->heure_debut);
                $heureFin = (clone $heureDebut)->addMinutes((int) $reservation->duree);
                
                $debutReservation = \Carbon\Carbon::parse($reservation->date_reservation . ' ' . $heureDebut->format('H:i:s'));
                $finReservation = \Carbon\Carbon::parse($reservation->date_reservation . ' ' . $heureFin->format('H:i:s'));
                
                return ($dateDebut < $finReservation && $dateFin > $debutReservation);
            });
        
        if ($reservationsConflits->count() > 0) {
            return back()->withInput()->with('error', 'Il y a des réservations existantes pour cette machine pendant cette période. Veuillez choisir un autre créneau.');
        }
        
        // Créer la maintenance
        $maintenance = Maintenance::create([
            'machine_id' => $request->machine_id,
            'technicien_id' => $technicien->id,
            'type' => $request->type,
            'description' => $request->description,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'statut' => Maintenance::STATUT_PLANIFIEE,
        ]);
        
        // Ajouter un commentaire initial
        MaintenanceComment::create([
            'maintenance_id' => $maintenance->id,
            'technicien_id' => $technicien->id,
            'contenu' => "Maintenance planifiée"
        ]);
        
        return redirect()->route('technicien.maintenances-technicien.index')
            ->with('success', 'Maintenance planifiée avec succès.');
    }
}
