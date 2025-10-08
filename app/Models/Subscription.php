<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'formation_id',
        'course_id',
        'chapter_id',
        'type',
        'price',
        'payment_reference',
        'is_validated',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withoutTrashed();
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withoutTrashed();
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withoutTrashed();
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class)->withoutTrashed();
    }
}
