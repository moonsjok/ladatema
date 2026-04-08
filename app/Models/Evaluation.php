<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['evaluatable_type', 'evaluatable_id', 'title', 'description'];

    public function evaluatable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->withoutTrashed();
    }
}
