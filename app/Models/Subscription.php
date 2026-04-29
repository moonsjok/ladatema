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
     * Vérifier si la souscription est expirée
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si la souscription est active
     */
    public function isActive()
    {
        return $this->is_validated && !$this->isExpired();
    }

    /**
     * Obtenir le nombre de jours restants
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        return $this->expires_at->diffInDays(now());
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
            $this->expires_at = $this->expires_at->addDays($days);
        } else {
            $this->expires_at = now()->addDays($days);
        }
        
        $this->duration_in_days = $this->expires_at->diffInDays(now());
        $this->save();
    }
}
