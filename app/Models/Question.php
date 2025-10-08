<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['evaluation_id', 'type', 'question_text'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class)->withoutTrashed();
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->withoutTrashed();
    }
}
