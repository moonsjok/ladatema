<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['formation_id', 'title', 'description'];

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withoutTrashed();
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->withoutTrashed();
    }
    public function evaluation()
    {
        return $this->morphOne(Evaluation::class, 'evaluatable')->withoutTrashed();
    }

    /**
     * Relationship with Souscriptions.
     */
    public function souscriptions()
    {
        return $this->hasMany(Subscription::class)->withoutTrashed();
    }

    protected static function booted()
    {
        static::deleting(function ($course) {
            // Soft delete des chapitres liés
            $course->chapters()->each(function ($chapter) {
                $chapter->delete(); // Soft delete des chapitres
            });
        });
    }
}
