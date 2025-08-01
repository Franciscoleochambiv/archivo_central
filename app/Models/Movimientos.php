<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    use HasFactory;
    protected $fillable = ['registro_documental_id', 'tipo', 'usuario', 'comentario', 'fecha'];

    public function registro()
    {
        return $this->belongsTo(RegistroDocumental::class, 'registro_documental_id');
    }
}
