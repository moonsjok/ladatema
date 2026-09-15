<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
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
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si le compte est désactivé par l'administration
        if (isset($user->is_active) && $user->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé par l\'administration. Veuillez contacter le support pour plus d\'informations.');
        }

        // Vérifier si l'adresse e-mail est vérifiée ET si le profil est complet
        $isEmailVerified = $user->hasVerifiedEmail();
        $isProfileComplete = !empty($user->nom) &&
                             !empty($user->prenoms) &&
                             !empty($user->phone_call) &&
                             !empty($user->phone_whatsapp);

        if (!$isEmailVerified || !$isProfileComplete) {
            // Exclure les routes de vérification, de déconnexion et de mise à jour de profil pour éviter les boucles de redirection
            if ($request->routeIs('verification.*', 'logout', 'profile.complete*')) {
                return $next($request);
            }

            return redirect()->route('verification.notice')
                ->with('info', 'Vous devez vérifier votre e-mail et compléter l\'ensemble de vos informations de profil pour continuer.');
        }

        return $next($request);
    }
}
