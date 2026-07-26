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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (is_null($subscription->duration_in_days)) {
                $subscription->duration_in_days = 90;
            }
            if (is_null($subscription->expires_at) && $subscription->duration_in_days) {
                $subscription->expires_at = now()->addDays($subscription->duration_in_days);
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
            // En cas d'erreur de parsing, considérer comme expiré par sécurité
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
        return $this->is_validated && !$this->isExpired();
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
            // Validation et conversion sécurisée de la date d'expiration
            $expiresAt = $this->expires_at;
            if (is_string($expiresAt)) {
                $expiresAt = \Carbon\Carbon::parse($expiresAt);
            }
            
            // Validation et conversion de la date actuelle
            $now = now();
            
            // Calculer la différence en jours et heures
            $diff = $now->diff($expiresAt);
            
            // Si la souscription est expirée, retourner 0
            if ($expiresAt->isPast()) {
                return 0;
            }
            
            // Si plus de 365 jours, afficher en jours uniquement
            if ($diff->days >= 365) {
                return round($diff->days) . ' jours';
            }
            
            // Si plus d'un jour, afficher en jours et heures
            if ($diff->days > 0) {
                return round($diff->days) . ' jours, ' . $diff->h . 'h';
            }
            
            // Si moins d'un jour, afficher en heures uniquement
            return $diff->h . 'h';
            
        } catch (\Exception $e) {
            // En cas d'erreur de parsing, retourner une valeur par défaut
            \Log::error('Erreur de calcul temps restant: ' . $e->getMessage(), [
                'subscription_id' => $this->id,
                'expires_at' => $this->expires_at
            ]);
            return 'Erreur';
        }
    }

    /**
     * Calculer la date d'expiration basée sur la durée
     */
    public function calculateExpirationDate()
    {
        if ($this->duration_in_days) {
            $this->expires_at = now()->addDays($this->duration_in_days);
            $this->save();
        }
    }

    /**
     * Étendre la souscription
     */
    public function extend(int $days)
    {
        if ($this->expires_at) {
            // Gérer le cas où expires_at est une chaîne de caractères
            if (is_string($this->expires_at)) {
                $currentExpiresAt = \Carbon\Carbon::parse($this->expires_at);
            } else {
                $currentExpiresAt = $this->expires_at;
            }
            $this->expires_at = $currentExpiresAt->addDays($days);
        } else {
            $this->expires_at = now()->addDays($days);
        }
        
        // Mettre à jour la durée totale depuis la création, toujours positive
        if ($this->created_at) {
            $this->duration_in_days = max(1, $this->created_at->diffInDays($this->expires_at));
        } else {
            $this->duration_in_days = max(1, now()->diffInDays($this->expires_at));
        }
        
        $this->save();
    }
}
