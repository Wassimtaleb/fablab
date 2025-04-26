<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        if (!$user->has_subscription) {
            return redirect()->route('user.subscription.form')
                ->with('error', 'Vous devez avoir un abonnement actif pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
} 