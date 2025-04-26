<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Technicien;
use App\Models\Machine;
use App\Models\Maintenance;
use App\Models\Message;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class TechnicienController extends Controller
{
    public function dashboard()
    {
        $technicien = Auth::guard('technicien')->user();
        
        // Récupérer toutes les machines
        $machines = Machine::all();
            
        // Récupérer les machines en maintenance par ce technicien
        $machinesEnMaintenance = Maintenance::where('technicien_id', $technicien->id)
            ->where('statut', 'en_cours')
            ->with('machine')
            ->get();
            
        // Récupérer les messages non lus
        $messagesNonLus = Message::where('destinataire_id', $technicien->id)
            ->where('destinataire_type', Technicien::class)
            ->where('lu', false)
            ->count();
            
        // Récupérer les réservations actives pour chaque machine
        $dateAujourdhui = now()->format('Y-m-d');
        $heureActuelle = now()->format('H:i:s');
        $reservationsParMachine = [];
        
        foreach ($machines as $machine) {
            $reservations = Reservation::where('machine_id', $machine->id)
                ->where('statut', 'validee')
                ->where(function($query) use ($dateAujourdhui, $heureActuelle) {
                    // Réservations futures (date après aujourd'hui)
                    $query->where('date_reservation', '>', $dateAujourdhui)
                    // OU réservations d'aujourd'hui avec une heure future
                    ->orWhere(function($q) use ($dateAujourdhui, $heureActuelle) {
                        $q->where('date_reservation', '=', $dateAujourdhui)
                          ->whereRaw('CONCAT(heure_debut, ":00") > ?', [$heureActuelle]);
                    });
                })
                ->orderBy('date_reservation')
                ->orderBy('heure_debut')
                ->get();
                
            $reservationsParMachine[$machine->id] = $reservations;
        }
        
        return view('technicien.dashboard', compact(
            'technicien',
            'machines',
            'machinesEnMaintenance',
            'messagesNonLus',
            'reservationsParMachine'
        ));
    }

    public function index()
    {
        $techniciens = Technicien::all();
        return view('technicien.index', compact('techniciens'));
    }

    public function create()
    {
        return view('technicien.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('techniciens.index')->with('success', 'Technicien créé avec succès');
    }

    public function show(Technicien $technicien)
    {
        return view('technicien.show', compact('technicien'));
    }

    public function edit(Technicien $technicien)
    {
        return view('technicien.edit', compact('technicien'));
    }

    public function update(Request $request, Technicien $technicien)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:techniciens,email,' . $technicien->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $technicien->update($data);

        return redirect()->route('techniciens.index')->with('success', 'Technicien mis à jour avec succès');
    }

    public function destroy(Technicien $technicien)
    {
        $technicien->delete();
        return redirect()->route('techniciens.index')->with('success', 'Technicien supprimé avec succès');
    }

    public function profile()
    {
        if (!Auth::guard('technicien')->check() || session('user_type') !== 'technicien') {
            return redirect('/');
        }
        return view('technicien.profile', ['technicien' => Auth::guard('technicien')->user()]);
    }

    public function updateProfile(Request $request)
    {
        if (!Auth::guard('technicien')->check() || session('user_type') !== 'technicien') {
            return redirect('/');
        }

        $technicien = Auth::guard('technicien')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:techniciens,email,' . $technicien->id,
        ]);

        $technicien->update($request->only(['name', 'email']));

        return redirect()->route('technicien.profile')->with('success', 'Profil mis à jour avec succès');
    }

    // Afficher le formulaire de connexion technicien
    public function showLoginForm()
    {
        return view('technicien.login');
    }

    // Traiter la connexion technicien
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::guard('technicien')->attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('user_type', 'technicien');
            return redirect()->route('technicien.dashboard')->with('success', 'Connexion réussie.');
        }
        return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
    }
}
