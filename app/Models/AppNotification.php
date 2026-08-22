<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'sender_id',
        'target_type',
        'target_user_id',
        'title',
        'message',
        'is_important',
    ];

    protected $casts = [
        'is_important' => 'boolean',
    ];

    /**
     * Expéditeur de la notification (Dev ou Owner)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Utilisateur spécifique ciblé (si target_type === 'user')
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Historique des lectures
     */
    public function reads()
    {
        return $this->hasMany(AppNotificationRead::class, 'notification_id');
    }

    /**
     * Vérifier si un utilisateur a lu cette notification
     */
    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }
}
