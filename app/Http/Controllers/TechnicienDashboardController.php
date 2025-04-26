<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicienDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du technicien
     */
    public function index()
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Récupérer les maintenances du technicien
        $maintenancesEnCours = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', Maintenance::STATUT_EN_COURS)
            ->with('machine')
            ->orderBy('date_debut', 'desc')
            ->limit(5)
            ->get();
            
        $maintenancesPlanifiees = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', Maintenance::STATUT_PLANIFIEE)
            ->with('machine')
            ->orderBy('date_debut', 'asc')
            ->limit(5)
            ->get();
        
        // Statistiques
        $statsMaintenances = [
            'en_cours' => Maintenance::where('technicien_id', $technicien->id)
                ->where('statut', Maintenance::STATUT_EN_COURS)
                ->count(),
            'planifiees' => Maintenance::where('technicien_id', $technicien->id)
                ->where('statut', Maintenance::STATUT_PLANIFIEE)
                ->count(),
            'terminees' => Maintenance::where('technicien_id', $technicien->id)
                ->where('statut', Maintenance::STATUT_TERMINEE)
                ->count(),
            'total' => Maintenance::where('technicien_id', $technicien->id)->count()
        ];
        
        return view('technicien.dashboard', compact(
            'technicien',
            'maintenancesEnCours',
            'maintenancesPlanifiees',
            'statsMaintenances'
        ));
    }
}
