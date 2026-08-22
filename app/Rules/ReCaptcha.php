<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptcha implements ValidationRule
{
    protected string $action;
    protected float $minScore;

    /**
     * @param string $action Nom de l'action attendue (ex: 'contact')
     * @param float $minScore Score minimum requis entre 0.0 et 1.0 (par défaut 0.5)
     */
    public function __construct(string $action = 'contact', float $minScore = 0.5)
    {
        $this->action = $action;
        $this->minScore = $minScore;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('La vérification de sécurité reCAPTCHA est requise.');
            return;
        }

        $secretKey = config('services.recaptcha.secret_key');

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $json = $response->json();

            if (!$response->successful() || !($json['success'] ?? false)) {
                $fail('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
                return;
            }

            // Vérification du score v3 (si retourné par l'API Google)
            if (isset($json['score']) && $json['score'] < $this->minScore) {
                Log::warning('Score reCAPTCHA v3 suspect détecté: ' . $json['score']);
                $fail('Comportement automatisé détecté. Envoi refusé.');
                return;
            }

            // Vérification du nom d'action v3 (si retourné par l'API Google)
            if (isset($json['action']) && $this->action && $json['action'] !== $this->action) {
                Log::warning("Action reCAPTCHA v3 invalide. Attendu: {$this->action}, reçu: {$json['action']}");
                $fail('Action reCAPTCHA invalide.');
                return;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification reCAPTCHA v3: ' . $e->getMessage());
            $fail('Impossible de vérifier le reCAPTCHA. Veuillez réessayer ultérieurement.');
        }
    }
}
