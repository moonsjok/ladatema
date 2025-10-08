<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes; // Ajouter SoftDeletes
    protected $table = 'profiles';
    protected $fillable = [
        'user_id',
        'photo',
        'phone',
        'address',
        'birth_date',
        'bio',
    ];

    /**
     * Relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withoutTrashed();
    }
}
