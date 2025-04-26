<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::with(['reservations' => function($query) {
            $query->where('statut', 'validée')
                  ->where('date_reservation', '>=', now());
        }])->get();

        // Mettre à jour le statut des machines en fonction des réservations
        foreach ($machines as $machine) {
            if ($machine->reservations->isNotEmpty()) {
                $machine->statut = 'non_disponible';
                $machine->save();
            }
        }

        return view('machines.index', compact('machines'));
    }

    public function userIndex()
    {
        // Récupérer toutes les machines avec leurs réservations actives
        $machines = Machine::with(['reservations' => function($query) {
            $query->where('statut', 'validée')
                  ->where('date_reservation', '>=', now());
        }])->orderBy('nom')->get();
        
        return view('user.machines.index', compact('machines'));
    }

    public function create()
    {
        return view('machines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|in:disponible,non_disponible,hors_service',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $machine = new Machine();
        $machine->nom = $request->input('nom');
        $machine->description = $request->input('description');
        $machine->statut = $request->input('statut');
        
        // Définir disponible en fonction du statut
        $machine->disponible = ($request->input('statut') === 'disponible');
        
        // Traitement de l'image
        if ($request->hasFile('image')) {
            $imageName = strtolower(str_replace(' ', '-', $machine->nom)) . '.' . $request->image->extension();
            $request->image->move(public_path('images/machines'), $imageName);
            $machine->image = $imageName;
        }
        
        $machine->save();

        return redirect()->route('admin.machines.index')->with('success', 'Machine ajoutée avec succès');
    }

    public function show(Machine $machine)
    {
        return view('machines.show', compact('machine'));
    }

    public function edit(Machine $machine)
    {
        return view('machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|in:disponible,non_disponible,hors_service',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        
        // Gérer la cohérence entre statut et disponibilité
        $data['disponible'] = ($data['statut'] === 'disponible');
        
        // Traitement de l'image
        if ($request->hasFile('image')) {
            $imageName = strtolower(str_replace(' ', '-', $request->nom)) . '.' . $request->image->extension();
            $request->image->move(public_path('images/machines'), $imageName);
            $data['image'] = $imageName;
        }

        $machine->update($data);
        return redirect()->route('admin.machines.manage')->with('success', 'Machine mise à jour avec succès');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('admin.machines.manage')->with('success', 'Machine supprimée avec succès');
    }
    
    /**
     * Affiche la page de gestion des machines (modification et suppression)
     */
    public function manage()
    {
        $machines = Machine::orderBy('nom')->get();
        return view('machines.manage', compact('machines'));
    }
} 