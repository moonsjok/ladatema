<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'formation_id',
        'course_id',
        'chapter_id',
        'type',
        'price',
        'payment_reference',
        'is_validated',
        'duration_in_days',
        'expires_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime',
        'duration_in_days' => 'integer',
        'is_validated' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            // Règle métier : Durée minimum de 90 jours (3 mois)
            if (empty($subscription->duration_in_days) || (int)$subscription->duration_in_days < 90) {
                $subscription->duration_in_days = 90;
            }

            // Calcul automatique de la date d'expiration sans muter la date de création
            if (empty($subscription->expires_at)) {
                $baseDate = $subscription->created_at ? (clone $subscription->created_at) : now();
                $subscription->expires_at = (clone $baseDate)->addDays((int)$subscription->duration_in_days);
            }
        });

        static::updating(function ($subscription) {
            // Garantir 90 jours minimum en cas de mise à jour
            if (!empty($subscription->duration_in_days) && (int)$subscription->duration_in_days < 90) {
                $subscription->duration_in_days = 90;
            }

            // Calculer/recalculer la date d'expiration de manière sécurisée si nécessaire
            if (empty($subscription->expires_at) || $subscription->isDirty('duration_in_days')) {
                $baseDate = $subscription->created_at ? (clone $subscription->created_at) : now();
                $subscription->expires_at = (clone $baseDate)->addDays((int)($subscription->duration_in_days ?? 90));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutTrashed();
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withoutTrashed();
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withoutTrashed();
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class)->withoutTrashed();
    }

    /**
     * Vérifier si la souscription est expirée avec validation sécurisée
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }

        try {
            // Validation et conversion sécurisée de la date d'expiration
            $expiresAt = $this->expires_at;
            if (is_string($expiresAt)) {
                $expiresAt = \Carbon\Carbon::parse($expiresAt);
            }

            return $expiresAt->isPast();

        } catch (\Exception $e) {
            \Log::error('Erreur de vérification expiration: ' . $e->getMessage(), [
                'subscription_id' => $this->id,
                'expires_at' => $this->expires_at
            ]);
            return true;
        }
    }

    /**
     * Vérifier si la souscription est active
     */
    public function isActive()
    {
        return (bool)$this->is_validated && !$this->isExpired();
    }

    /**
     * Obtenir le temps restant formaté avec validation sécurisée
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        try {
            $expiresAt = $this->expires_at;
            if (is_string($expiresAt)) {
                $expiresAt = \Carbon\Carbon::parse($expiresAt);
            }

            $now = now();
            if ($expiresAt->isPast()) {
                return 0;
            }

            $diff = $now->diff($expiresAt);

            if ($diff->days >= 365) {
                return round($diff->days) . ' jours';
            }

            if ($diff->days > 0) {
                return round($diff->days) . ' jours, ' . $diff->h . 'h';
            }

            return $diff->h . 'h';

        } catch (\Exception $e) {
            \Log::error('Erreur de calcul temps restant: ' . $e->getMessage(), [
                'subscription_id' => $this->id,
                'expires_at' => $this->expires_at
            ]);
            return 'Erreur';
        }
    }

    /**
     * Calculer et enregistrer la date d'expiration basée sur la durée (minimum 90 jours)
     */
    public function calculateExpirationDate()
    {
        $duration = max(90, (int)($this->duration_in_days ?? 90));
        $this->duration_in_days = $duration;
        $baseDate = $this->created_at ? (clone $this->created_at) : now();
        $this->expires_at = (clone $baseDate)->addDays($duration);
        $this->save();
    }

    /**
     * Étendre la souscription de X jours supplémentaires sans erreur
     */
    public function extend(int $days)
    {
        $additionalDays = max(1, $days);

        if ($this->expires_at) {
            $currentExpiresAt = is_string($this->expires_at)
                ? \Carbon\Carbon::parse($this->expires_at)
                : (clone $this->expires_at);

            if ($currentExpiresAt->isFuture()) {
                // Souscription encore valide : ajouter les jours à la date d'expiration actuelle
                $this->expires_at = (clone $currentExpiresAt)->addDays($additionalDays);
            } else {
                // Souscription déjà expirée : repartir de la date du jour (now) + les jours ajoutés
                $this->expires_at = now()->addDays($additionalDays);
            }
        } else {
            $this->expires_at = now()->addDays(max(90, $additionalDays));
        }

        $baseDate = $this->created_at ? (clone $this->created_at) : now();
        $this->duration_in_days = max(90, (int)$baseDate->diffInDays($this->expires_at));

        // Activer/valider la souscription
        $this->is_validated = true;

        $this->save();
    }
}
