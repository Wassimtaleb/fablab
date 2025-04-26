<?php

namespace App\Http\Controllers\Technicien;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Maintenance;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MachineMaintenanceController extends Controller
{
    /**
     * Mettre une machine en maintenance
     */
    public function mettreEnMaintenance(Request $request, Machine $machine)
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Vérifier si la machine est disponible
        if (!$machine->disponible || $machine->statut !== 'disponible') {
            return redirect()->route('technicien.dashboard')
                ->with('error', 'Cette machine n\'est pas disponible pour maintenance.');
        }
        
        // Vérifier s'il y a des réservations validées pour cette machine
        $reservationsValidees = Reservation::where('machine_id', $machine->id)
            ->where('statut', 'validee')
            ->where(function($query) {
                // Vérifier les réservations d'aujourd'hui ou futures
                $query->where('date_reservation', '>', now()->format('Y-m-d'))
                      ->orWhere(function($q) {
                          // Pour aujourd'hui, vérifier si l'heure de début est future
                          $q->where('date_reservation', '=', now()->format('Y-m-d'))
                            ->whereRaw('CONCAT(heure_debut, ":00") > ?', [now()->format('H:i:s')]);
                      });
            })
            ->exists();
            
        if ($reservationsValidees) {
            return redirect()->route('technicien.dashboard')
                ->with('error', 'Cette machine a des réservations validées et ne peut pas être mise en maintenance.');
        }
        
        // Mettre la machine en maintenance
        DB::transaction(function () use ($machine, $technicien) {
            // Mettre à jour le statut de la machine
            $machine->update([
                'statut' => 'en_maintenance'
            ]);
            
            // Créer un enregistrement de maintenance
            Maintenance::create([
                'machine_id' => $machine->id,
                'technicien_id' => $technicien->id,
                'type' => 'Maintenance technique',
                'description' => 'Maintenance technique par ' . $technicien->nom . ' ' . $technicien->prenom,
                'date_debut' => now(),
                'statut' => 'en_cours'
            ]);
        });
        
        return redirect()->route('technicien.dashboard')
            ->with('success', 'La machine a été mise en maintenance avec succès.');
    }
    
    /**
     * Terminer la maintenance d'une machine
     */
    public function terminerMaintenance(Request $request, Maintenance $maintenance)
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Vérifier que la maintenance appartient au technicien connecté
        if ($maintenance->technicien_id !== $technicien->id) {
            return redirect()->route('technicien.dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à terminer cette maintenance.');
        }
        
        // Terminer la maintenance
        DB::transaction(function () use ($maintenance) {
            // Mettre à jour le statut de la maintenance
            $maintenance->update([
                'statut' => 'terminee',
                'date_fin' => now()
            ]);
            
            // Remettre la machine en disponible
            $maintenance->machine->update([
                'statut' => 'disponible'
            ]);
        });
        
        return redirect()->route('technicien.dashboard')
            ->with('success', 'La maintenance a été terminée avec succès.');
    }
}
