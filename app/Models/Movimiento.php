<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_trabajador',
        'numero_nomina',
        'remuneracion_basica',
        'dias_laborados',
        'aguinaldo',
        'vacaciones',
        'cts',
        'remuneracion_mensual',
        'aportes_cts',
        'renta_5ta',
        'descuentos_total',
        'essalud',
        'sctr',
        'total_planilla'
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'id_trabajador');
    }
}
