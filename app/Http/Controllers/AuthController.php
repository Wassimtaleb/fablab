<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Technicien;
use App\Models\Abonnement;

class AuthController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $userType = $request->input('user_type', 'user');

        // Si l'utilisateur tente de se connecter en tant qu'admin
        if ($userType === 'admin') {
            if ($admin = Admin::where('email', $credentials['email'])->first()) {
                if (password_verify($credentials['password'], $admin->password)) {
                    // Régénérer la session
                    $request->session()->regenerate();
                    
                    // Connecter l'admin
                    Auth::guard('admin')->login($admin);
                    
                    // Définir le type d'utilisateur
                    $request->session()->put('user_type', 'admin');
                    
                    return redirect()->route('admin.dashboard')
                           ->with('success', 'Connexion admin réussie');
                }
            }
            return back()->withErrors(['email' => 'Identifiants admin incorrects'])
                        ->withInput($request->only('email'));
        }
        
        // Si l'utilisateur tente de se connecter en tant que technicien
        if ($userType === 'technicien') {
            if ($technicien = Technicien::where('email', $credentials['email'])->first()) {
                if (password_verify($credentials['password'], $technicien->password)) {
                    // Régénérer la session
                    $request->session()->regenerate();
                    
                    // Connecter le technicien
                    Auth::guard('technicien')->login($technicien);
                    
                    // Définir le type d'utilisateur
                    $request->session()->put('user_type', 'technicien');
                    
                    return redirect()->route('technicien.dashboard')
                           ->with('success', 'Connexion technicien réussie');
                }
            }
            return back()->withErrors(['email' => 'Identifiants technicien incorrects'])
                        ->withInput($request->only('email'));
        }
        
        // Si l'utilisateur tente de se connecter en tant qu'utilisateur standard
        if ($user = User::where('email', $credentials['email'])->first()) {
            if (password_verify($credentials['password'], $user->password)) {
                // Régénérer la session
                $request->session()->regenerate();
                
                // Connecter l'utilisateur
                Auth::guard('web')->login($user);
                
                // Définir le type d'utilisateur
                $request->session()->put('user_type', 'user');

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

                $abonnementEnAttente = Abonnement::where('user_id', $user->id)
                                               ->where('statut', 'en_attente')
                                               ->first();

                if ($abonnementEnAttente) {
                    return redirect()->route('user.dashboard')
                           ->with('info', 'Votre demande d\'abonnement est en attente de validation.');
                }

                if (!$abonnementActif) {
                    return redirect()->route('user.subscription.form')
                           ->with('info', 'Votre abonnement n\'est pas actif. Veuillez souscrire à un nouvel abonnement.');
                }

                return redirect()->route('user.dashboard')
                       ->with('success', 'Connexion réussie');
            }
        }

        return back()->withErrors(['email' => 'Identifiants incorrects'])
                    ->withInput($request->only('email'));
    }

    protected function hasActiveSubscription($userId)
    {
        return Abonnement::where('user_id', $userId)
                       ->where('statut', 'actif')
                       ->exists();
    }

    public function logout(Request $request)
    {
        // Déconnecter de tous les guards
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('technicien')->logout();
        
        // Invalider la session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')
                ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}