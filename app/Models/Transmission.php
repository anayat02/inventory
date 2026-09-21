<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\in_characteristics_for_product;

class Transmission extends Model
{
    use HasFactory;

    protected $table = 'transmissions';
    protected $primaryKey = 'id_trans';

    public function characteristics()
    {
        return $this->hasMany(in_characteristics_for_product::class, 'id_product', 'id_product');
    }
}
