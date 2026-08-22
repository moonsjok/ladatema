<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;

class EnsureSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Vérifie si l'utilisateur est connecté, a le rôle "student", et une souscription validée
        if (!$user || !$user->hasRole('student')) {
            abort(403, 'Accès réservé aux étudiants.');
        }

        $formationId = $request->route('formation'); // ID de la formation dans la route
        $subscription = Subscription::where('user_id', $user->id)
            ->where('formation_id', $formationId)
            ->latest()
            ->first();

        if (!$subscription) {
            abort(403, 'Vous devez souscrire à cette formation pour y accéder.');
        }

        if ($subscription->isExpired()) {
            return redirect()->route('subscriptions.expired', $subscription->id);
        }

        if (!$subscription->is_validated) {
            return redirect()->route('dashboard')->with('info', 'Votre souscription est en attente de validation.');
        }

        return $next($request);
    }
}
