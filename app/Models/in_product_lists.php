<?php

namespace App\Models;

use App\Models\in_characteristics_for_product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class in_product_lists extends Model
{

    public $timestamps = false;

    protected $table = 'in_product_lists';
    protected $primaryKey = 'id_product';

    public function characteristics()
    {
        return $this->hasMany(in_characteristics_for_product::class, 'id_product', 'id_product');
    }

    protected $fillable = [
        'inv_number',
        'status',
        'verification_status',
        'current_status',
        'actual_inventory',
        'auditoryID',
        'buildingID',
        'id_name',
        'type',
        'manufacture_year',
        'repair_count',
        'movement_count',
        'needs_replacement',
    ];

}
