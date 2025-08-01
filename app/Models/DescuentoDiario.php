<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescuentoDiario extends Model
{
    use HasFactory;

    protected $table = 'descuentos_diarios';

    protected $fillable = [
        'personal_id',
        'fecha',
        'descuento_morning_in',
        'descuento_morning_out',
        'descuento_afternoon_in',
        'descuento_afternoon_out',
        'motivo',
    ];

    // Relación con la tabla 'personal'
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }
}
