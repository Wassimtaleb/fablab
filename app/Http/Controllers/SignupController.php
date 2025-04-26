<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SignupController extends Controller
{
    /**
     * Affiche le formulaire d'inscription
     */
    public function showSignupForm()
    {
        return view('user.signup');
    }

    /**
     * Traite la demande d'inscription
     */
    public function signup(Request $request)
    {
        // Valider les données du formulaire
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Créer l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'password' => Hash::make($request->password),
            ]);

            Log::info('Nouvel utilisateur inscrit', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->route('user.login')
                ->with('success', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription d\'un utilisateur', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.')
                ->withInput();
        }
    }
}
