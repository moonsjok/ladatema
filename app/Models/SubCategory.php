<?php

// app/Models/SubCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'sub_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutTrashed();
    }

    public function formations()
    {
        return $this->hasMany(Formation::class)->withoutTrashed();
    }

    protected static function booted()
    {
        static::deleting(function ($subcategory) {
            // Soft delete des formations liées
            $subcategory->formations()->each(function ($formation) {
                $formation->delete(); // Soft delete des formations
            });
        });
    }
}
