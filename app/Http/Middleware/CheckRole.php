<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérification si l'utilisateur est authentifié
        if (!Auth::check()) {
            // Redirection vers la page de login si non authentifié
            return Redirect::route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer le rôle de l'utilisateur (supposant qu'un utilisateur n'a qu'un seul rôle)
        $userRole = Auth::user()->role;

        // Vérification si le rôle de l'utilisateur est dans les rôles autorisés
        if (in_array($userRole, $roles)) {
            // Si l'utilisateur a le rôle "dev", il a tous les droits
            if ($userRole === 'dev') {
                return $next($request);
            }
            // Si l'utilisateur a le rôle "owner", il a accès mais avec des restrictions potentielles
            elseif ($userRole === 'owner') {
                // Ici, vous pourriez ajouter des vérifications supplémentaires ou des restrictions spécifiques pour le rôle "owner"
                return $next($request);
            }
        }

        // Si l'utilisateur n'a pas le rôle requis, rediriger avec un message d'erreur
        return Redirect::back()->with('error', 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
    }
}
