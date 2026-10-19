<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $table = 'laptop_issuance';

    protected $fillable = ['id', 'full_name', 'model', 'serial_number', 'file_act'];
}
