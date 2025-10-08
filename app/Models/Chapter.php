<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['course_id', 'numero', 'title', 'content'];

    public function course()
    {
        return $this->belongsTo(Course::class)->withoutTrashed();
    }
    public function evaluation()
    {
        return $this->morphOne(Evaluation::class, 'evaluatable');
    }

    /**
     * Relationship with Souscriptions.
     */
    public function souscriptions()
    {
        return $this->hasMany(Subscription::class)->withoutTrashed();
    }
}
