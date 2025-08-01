<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uit extends Model
{
    use HasFactory;
    protected $fillable = ['anio', 'valor', 'ruc', 'entidad'];
}
