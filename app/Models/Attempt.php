<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'evaluation_id',
        'user_id',
        'score',
        'total_points',
        'pourcentage',
        'grade',
        'passed',
        'started_at',
        'completed_at',
        'time_spent' // en secondes
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    // Calcule le grade en fonction du pourcentage
    public function getGradeAttribute()
    {
        $pourcentage = $this->pourcentage;
        
        if ($pourcentage >= 90) return 'A+';
        if ($pourcentage >= 80) return 'A';
        if ($pourcentage >= 70) return 'B';
        if ($pourcentage >= 60) return 'C';
        return 'F';
    }
}
