<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use App\Models\Technicien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userType = session('user_type');
        
        // Récupérer les messages reçus
        $messagesRecus = Message::where('destinataire_id', $user->id)
            ->where('destinataire_type', $userType === 'admin' ? Admin::class : ($userType === 'technicien' ? Technicien::class : User::class))
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Récupérer les messages envoyés
        $messagesEnvoyes = Message::where('expediteur_id', $user->id)
            ->where('expediteur_type', $userType === 'admin' ? Admin::class : ($userType === 'technicien' ? Technicien::class : User::class))
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('messages.index', compact('messagesRecus', 'messagesEnvoyes'));
    }
    
    public function create()
    {
        $userType = session('user_type');
        
        // Récupérer les destinataires possibles en fonction du type d'utilisateur
        $destinataires = [];
        
        if ($userType === 'admin') {
            $destinataires['techniciens'] = Technicien::all();
            $destinataires['utilisateurs'] = User::all();
        } elseif ($userType === 'technicien') {
            $destinataires['admins'] = Admin::all();
            $destinataires['utilisateurs'] = User::all();
        } else {
            $destinataires['admins'] = Admin::all();
            $destinataires['techniciens'] = Technicien::all();
        }
        
        return view('messages.create', compact('destinataires'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'destinataire_type' => 'required|string',
            'destinataire_id' => 'required|integer',
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
        ]);
        
        $user = Auth::user();
        $userType = session('user_type');
        
        $expediteurType = $userType === 'admin' ? Admin::class : ($userType === 'technicien' ? Technicien::class : User::class);
        
        Message::create([
            'expediteur_id' => $user->id,
            'expediteur_type' => $expediteurType,
            'destinataire_id' => $request->destinataire_id,
            'destinataire_type' => $request->destinataire_type,
            'sujet' => $request->sujet,
            'contenu' => $request->contenu,
            'lu' => false,
        ]);
        
        $routePrefix = $userType === 'admin' ? 'admin.' : ($userType === 'technicien' ? 'technicien.' : 'user.');
        return redirect()->route($routePrefix . 'messages.index')->with('success', 'Message envoyé avec succès');
    }
    
    public function show(Message $message)
    {
        // Marquer le message comme lu
        if (!$message->lu) {
            $message->update(['lu' => true]);
        }
        
        return view('messages.show', compact('message'));
    }
    
    public function destroy(Message $message)
    {
        $message->delete();
        
        $userType = session('user_type');
        $routePrefix = $userType === 'admin' ? 'admin.' : ($userType === 'technicien' ? 'technicien.' : 'user.');
        return redirect()->route($routePrefix . 'messages.index')->with('success', 'Message supprimé avec succès');
    }
} 