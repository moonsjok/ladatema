<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use App\Models\Subscription;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, InteractsWithMedia;

    /**
     * Envoyez la notification de vérification de l'adresse e-mail personnalisée.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Assigner le rôle 'student' par défaut aux nouveaux utilisateurs
        static::creating(function ($user) {
            if (empty($user->role)) {
                $user->role = 'student';
            }
        });
    }

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nom',
        'prenoms',
        'email',
        'password',
        'phone_call',
        'phone_whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Vérifie si l'utilisateur possède un rôle spécifique.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }


    /**
     * Relationship with Profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relationship with Souscriptions.
     */
    public function souscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Relationship with Attempts.
     */
    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    /**
     * Register media collections for User model
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile()
            ->registerMediaConversions(function (\Spatie\MediaLibrary\MediaCollections\Models\Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(100)
                    ->height(100)
                    ->sharpen(10);
            });

        $this
            ->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->registerMediaConversions(function (\Spatie\MediaLibrary\MediaCollections\Models\Media $media) {
                $this
                    ->addMediaConversion('thumbnail')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10);
            });

        $this
            ->addMediaCollection('videos')
            ->acceptsMimeTypes(['video/mp4', 'video/webm']);

        $this
            ->addMediaCollection('pdfs')
            ->acceptsMimeTypes(['application/pdf']);

        $this
            ->addMediaCollection('txt_files')
            ->acceptsMimeTypes(['text/plain', 'text/markdown']);

        $this
            ->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'text/plain', 'text/markdown'])
            ->singleFile();
    }
}
