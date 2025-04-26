<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Machine;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        $userType = session('user_type');
        
        if ($userType === 'technicien') {
            $technicien = Auth::guard('technicien')->user();
            $maintenances = Maintenance::where('technicien_id', $technicien->id)
                ->with(['machine', 'technicien'])
                ->orderBy('date_debut', 'desc')
                ->get();
        } else {
            $maintenances = Maintenance::with(['machine', 'technicien'])
                ->orderBy('date_debut', 'desc')
                ->get();
        }
        
        return view('maintenances.index', compact('maintenances'));
    }
    
    public function create()
    {
        $machines = Machine::all();
        $techniciens = Technicien::all();
        
        return view('maintenances.create', compact('machines', 'techniciens'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'technicien_id' => 'required|exists:techniciens,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|string|in:planifiee,en_cours,terminee,annulee',
            'notes' => 'nullable|string',
        ]);
        
        Maintenance::create($request->all());
        
        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance créée avec succès.');
    }
    
    public function show(Maintenance $maintenance)
    {
        return view('maintenances.show', compact('maintenance'));
    }
    
    public function edit(Maintenance $maintenance)
    {
        $machines = Machine::all();
        $techniciens = Technicien::all();
        
        return view('maintenances.edit', compact('maintenance', 'machines', 'techniciens'));
    }
    
    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'technicien_id' => 'required|exists:techniciens,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|string|in:planifiee,en_cours,terminee,annulee',
            'notes' => 'nullable|string',
        ]);
        
        $maintenance->update($request->all());
        
        return redirect()->route('maintenances.show', $maintenance)
            ->with('success', 'Maintenance mise à jour avec succès.');
    }
    
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        
        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance supprimée avec succès.');
    }
} 