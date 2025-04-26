<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier uniquement si l'utilisateur est authentifié avec le guard admin
        if (!Auth::guard('admin')->check()) {
            return redirect('/')
                ->with('error', "Vous devez être connecté en tant qu'administrateur.");
        }
        
        return $next($request);
    }
} 