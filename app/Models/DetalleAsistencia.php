<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAsistencia extends Model
{
    use HasFactory;

    protected $table = 'detalle_asistencia';

    protected $fillable = [
        'personal_id',
        'mes',
        'dias_habiles',
        'dias_trabajados',
        'descuento_aplicado',
        'remuneracion_final',
    ];

    // Relación con la tabla 'personal'
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

   
}
