<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'answer_text',
        'explanation',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class)->withoutTrashed();
    }

    /**
     * Obtenir le texte de la réponse pour l'affichage
     */
    public function getDisplayText()
    {
        return $this->answer_text;
    }

    /**
     * Vérifier si cette réponse est l'intrus (pour les questions type "find_intruder")
     */
    public function isIntruder()
    {
        return !$this->is_correct;
    }

    /**
     * Obtenir l'indicateur visuel pour les questions type "find_intruder"
     */
    public function getIntruderIndicator()
    {
        if ($this->question && $this->question->type === Question::TYPE_FIND_INTRUDER) {
            return $this->isIntruder() ? '🎯 (Intrus)' : '✓';
        }
        
        return $this->is_correct ? '✓ (Correct)' : '';
    }
}
