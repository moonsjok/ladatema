<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'category_id',
        'sub_category_id',
        'title',
        'description',
        'price'

    ];
    public function category()
    {
        return $this->belongsTo(Category::class)->withoutTrashed();
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, "sub_category_id")->withoutTrashed();
    }
    public function courses()
    {
        return $this->hasMany(Course::class)->withoutTrashed();
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


    protected static function booted()
    {
        static::deleting(function ($formation) {
            // Soft delete des cours liés
            $formation->courses()->each(function ($course) {
                $course->delete(); // Soft delete des cours
            });
        });
    }
}
