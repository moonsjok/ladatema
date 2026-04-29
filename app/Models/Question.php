<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    // Types de questions disponibles
    const TYPE_SINGLE_CHOICE = 'single_choice';
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_TEXT = 'text';
    const TYPE_FIND_INTRUDER = 'find_intruder';

    protected $fillable = ['evaluation_id', 'type', 'question_text', 'points'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class)->withoutTrashed();
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->withoutTrashed();
    }

    /**
     * Vérifier si la question nécessite des réponses prédéfinies
     */
    public function requiresAnswers()
    {
        return in_array($this->type, [
            self::TYPE_SINGLE_CHOICE,
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_FIND_INTRUDER
        ]);
    }

    /**
     * Vérifier si la question accepte plusieurs réponses correctes
     */
    public function allowsMultipleCorrectAnswers()
    {
        return in_array($this->type, [
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_FIND_INTRUDER
        ]);
    }

    /**
     * Obtenir le libellé du type de question
     */
    public function getTypeLabel()
    {
        return match($this->type) {
            self::TYPE_SINGLE_CHOICE => 'Choix unique',
            self::TYPE_MULTIPLE_CHOICE => 'Choix multiple',
            self::TYPE_TEXT => 'Texte',
            self::TYPE_FIND_INTRUDER => 'Trouver l\'intrus',
            default => 'Inconnu'
        };
    }
}
