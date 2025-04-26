<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Abonnement;
use App\Models\Machine;
use App\Models\Reservation;

class UserController extends Controller
{
    public function showSubscriptionForm()
    {
        $user = Auth::guard('web')->user();
        $demandeEnAttente = Abonnement::where('user_id', $user->id)
                                    ->where('statut', 'en_attente')
                                    ->exists();

        $config = config('abonnements');
        return view('user.subscription.form', [
            'demandeEnAttente' => $demandeEnAttente,
            'config' => $config
        ]);
    }
    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        
        // Vérifier si l'utilisateur a déjà eu un abonnement
        $hasEverSubscribed = Abonnement::where('user_id', $user->id)->exists();
        
        if (!$hasEverSubscribed) {
            return redirect()->route('user.subscription.form')
                           ->with('info', 'Pour accéder aux machines, vous devez d\'abord souscrire à un abonnement.');
        }

        // Vérifier l'état de l'abonnement actuel
        $abonnementActif = Abonnement::where('user_id', $user->id)
                                    ->where('statut', 'actif')
                                    ->first();
        if ($abonnementActif) {
            $abonnementActif->load('typeAbonnement');
        }

        $abonnementEnAttente = Abonnement::where('user_id', $user->id)
                                        ->where('statut', 'en_attente')
            ->first();

        // Récupérer les machines disponibles
        $machines = Machine::where('disponible', true)
                          ->where('statut', 'disponible')
                          ->get();

        // Récupérer les réservations en cours
        $reservations = Reservation::where('user_id', $user->id)
                                 ->whereIn('statut', ['en_attente', 'validee'])
                                 ->orderBy('date_reservation', 'desc')
                                 ->get();

        return view('user.dashboard', compact('abonnementActif', 'abonnementEnAttente', 'machines', 'reservations'));
    }

    public function checkSubscriptionStatus($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            Log::info('Statut d\'abonnement vérifié', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_subscription' => $user->has_subscription
            ]);
            return [
                'has_subscription' => $user->has_subscription,
                'email' => $user->email
            ];
        }
        return null;
    }

    public function profile()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        if (session('user_type') !== 'user') {
            return redirect()->back();
        }

        return view('user.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return redirect()->route('user.profile')->with('success', 'Profil mis à jour avec succès');
    }

    // Afficher le formulaire de connexion utilisateur
    public function showLoginForm()
    {
        return view('user.login');
    }

    // Traiter la connexion utilisateur
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('user_type', 'user');
            $user = Auth::guard('web')->user();
            $hasEverSubscribed = \App\Models\Abonnement::where('user_id', $user->id)->exists();
            if (!$hasEverSubscribed) {
                return redirect()->route('user.subscription.form')
                    ->with('info', "Pour accéder aux machines, vous devez d'abord souscrire à un abonnement.");
            }
            $abonnementActif = \App\Models\Abonnement::where('user_id', $user->id)
                ->where('statut', 'actif')->first();
            $abonnementEnAttente = \App\Models\Abonnement::where('user_id', $user->id)
                ->where('statut', 'en_attente')->first();
            if ($abonnementEnAttente) {
                return redirect()->route('user.dashboard')
                    ->with('info', "Votre demande d'abonnement est en attente de validation.");
            }
            if (!$abonnementActif) {
                return redirect()->route('user.subscription.form')
                    ->with('info', "Votre abonnement n'est pas actif. Veuillez souscrire à un nouvel abonnement.");
            }
            return redirect()->route('user.dashboard')->with('success', 'Connexion réussie.');
        }
        return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
    }
}
