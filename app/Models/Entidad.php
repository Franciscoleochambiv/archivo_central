<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;
     protected $table = 'entidades';

    protected $fillable = [
        'nombre',
        'tipo',
    ];

    // Relaciones (si las necesitas más adelante)
    public function registros()
    {
        return $this->hasMany(RegistroDocumental::class);
    }
}
