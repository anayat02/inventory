<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomCheckItem extends Model
{
    use HasFactory;

    protected $table = 'classroom_check_items';

    protected $fillable = [
        'check_id',
        'id_product',
        'product_name',
        'db_count',
        'fact_count',
        'inv_number',
        'is_present',
        'condition',
        'note',
    ];

    public function check()
    {
        return $this->belongsTo(ClassroomCheck::class, 'check_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(in_product_lists::class, 'id_product', 'id_product');
    }
}
