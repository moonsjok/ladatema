<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProtectMediaAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Accès non autorisé',
                'message' => 'Vous devez être connecté pour accéder à ce fichier.',
                'redirect' => route('login')
            ], 401);
        }

        return $next($request);
    }
}
