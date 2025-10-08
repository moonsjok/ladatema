<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use FedaPay\Customer;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        FedaPay::setApiKey(config('fedapay.live_secret_key'));
        FedaPay::setEnvironment(config('fedapay.mode')); // "sandbox" ou "live"
    }

    public function processPayment(Request $request)
    {
        // Valider les données reçues
        $request->validate([
            'prenom' => 'required|string|max:255',
            'email' => 'required|email',
            'amount' => 'required|numeric|min:1',
            // 'phone' => 'required|string', // Numéro de téléphone obligatoire
            // 'country' => 'required|string|min:2|max:2' // Code pays (ex: 'bj', 'tg')
        ]);

        try {


            // Recherche du client existant par email
            $response = Customer::all();
            $customers = $response->customers;
            $existingCustomer = null;

            foreach ($customers as $customer) {
                if ($customer->email === $request->email) {
                    $existingCustomer = $customer;
                    break;
                }
            }

            if ($existingCustomer) {
                // ✅ Client existant, on récupère son ID
                $customer = $existingCustomer;
            } else {
                // ❌ Le client n'existe pas, on le crée
                $customer = Customer::create([
                    "firstname" => $request->prenom,
                    "lastname" => "Doe",
                    "email" => $request->email,
                    "phone_number" => [
                        "number" => '64000001',
                        //     "country" => $request->country
                    ]
                ]);
                // Log de la réponse brute

            }
            // Log::debug('Réponse de l\'API : ', (array) $customer->id);

            // Création de la transaction
            $transaction = Transaction::create([
                "amount" => $request->amount,
                "description" => $request->description,
                "currency" => ["iso" => "XOF"],
                "customer" => ['id' => $customer->id],
                'callback_url' => route('subscriptions.confirm', ['type' => $request->subscription_type, 'typeid' => $request->subscription_typeid]),
            ]);
            // Log de la réponse brute
            //Log::debug('Réponse de l\'API : ', (array)  $transaction);
            //dd($transaction);
            // Génération du lien de paiement
            $token = $transaction->generateToken();
            // dd($token);
            return redirect($token->url);
        } catch (\Exception $e) {
            // Log de l'erreur pour analyse
            Log::error("Erreur lors du paiement : " . $e->getMessage());

            return back()->withErrors(['error' => 'Erreur lors du paiement : ' . $e->getMessage()]);
        }
    }
}
