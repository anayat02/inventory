<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomCheck extends Model
{
    use HasFactory;

    protected $table = 'classroom_checks';

    protected $fillable = [
        'tutor_id',
        'auditory_id',
        'check_date',
        'lesson_start',
        'lesson_finish',
        'check_type',
        'status',
        'comment',
        'keyboard_count',
        'mouse_count',
    ];

    public function items()
    {
        return $this->hasMany(ClassroomCheckItem::class, 'check_id', 'id');
    }
}
