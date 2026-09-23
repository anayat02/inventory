<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class in_messages extends Model
{
    use HasFactory;

    protected $fillable = ['id_message', 'id_product', 'id_name', 'inv_number', 'message', 'TutorID', 'updated_at'];

    public $timestamps = false;

    protected $table = 'in_messages';
    protected $primaryKey = 'id_message';
}
