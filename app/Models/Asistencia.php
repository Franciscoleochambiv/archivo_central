<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    protected $primaryKey = 'id'; // Asumiendo que la PK es 'id'

    protected $fillable = [
        'device_user_id',
        'fecha',
        'morning_in',
        'lunch_out',
        'afternoon_in',
        'afternoon_out',
        'latitud', // Nuevo campo
        'longitud' // Nuevo campo
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'device_user_id', 'id');
    }
}