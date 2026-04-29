<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'evaluatable_type', 
        'evaluatable_id', 
        'title', 
        'description',
        'duration',
        'total_questions',
        'scoring_mode',
        'passing_score',
        'max_attempts',
        'importance',
    ];

    public function evaluatable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->withoutTrashed();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    /**
     * Vérifier si l'évaluation est obligatoire
     */
    public function isMandatory()
    {
        return $this->importance === 'mandatory';
    }

    /**
     * Vérifier si l'évaluation est facultative
     */
    public function isOptional()
    {
        return $this->importance === 'optional';
    }

    /**
     * Obtenir le libellé de l'importance
     */
    public function getImportanceLabelAttribute()
    {
        return $this->importance === 'mandatory' ? 'Obligatoire' : 'Facultatif';
    }

    /**
     * Obtenir la couleur de l'importance pour l'affichage
     */
    public function getImportanceColorAttribute()
    {
        return $this->importance === 'mandatory' ? 'danger' : 'secondary';
    }
}
