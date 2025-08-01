<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagosOnline extends Model
{
    use HasFactory;

    protected $table = 'pagosonline';  // Nombre de la tabla
    protected $primaryKey = 'id';      // Llave primaria de la tabla
    protected $dates = ['fecha_vigencia']; 

    // Atributos que pueden ser asignados en masa
    protected $fillable = [
        'usuario_id',
        'monto',
        'token',
        'fecha_pago',
        'fecha_vigencia',
        'estado'
    ];
}
