<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SouscriptionCallbackController extends Controller
{
    /**
     * Gérer le retour de l'API tierce
     */
    public function handleCallback(Request $request)
    {
        try {
            $payload = $request->all(); // Récupération des données envoyées par FedaPay

            Log::info('Réponse du Webhook FedaPay : ', $payload);

            if (isset($payload['status']) && $payload['status'] === 'approved') {
                // Ici, tu mets à jour l'abonnement de l'utilisateur
                // Exemple : UserSubscription::where('transaction_id', $payload['id'])->update(['status' => 'paid']);
                return response()->json(['message' => 'Paiement validé !'], 200);
            }

            return response()->json(['message' => 'Paiement non validé'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
