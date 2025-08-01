<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DetallePlanilla extends Model
{
    use HasFactory;

    protected $table = 'detalle_planillas';


    protected $fillable = [
        'planilla_id',
        'personal_id',
        'idcargo',
        'idafp',
        'idonp',
        'dni',
        'fecha_ingreso',
        'dias_trabajados',
        'remuneracion_mensual',

        'ds311',
        'ds313',

        'aguinaldo',
        'remuneracion_vacacional',
        'cts',
        'descuentos_afp_fondo',
        'descuentos_afp_c',
        'descuentos_afp_ps',
        'descuentos_onp',
        'descuentos_r4',
        'descuentos_r5',
        'aportes_essalud',
        'aportes_sctr',
        'total_detalle',
        'flag',
    ];

    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'idcargo');
    }

    public function afp()
    {
        return $this->belongsTo(Afp::class, 'idafp');
    }

    public function onp()
    {
        return $this->belongsTo(Onp::class, 'idonp');
    }

    protected static function boot()
    {
        parent::boot();
    
        // Invalida la caché al crear
        static::created(function ($detallePlanilla) {
            // Invalida la caché de la planilla asociada
            Cache::forget("planilla_{$detallePlanilla->planilla_id}"); // Asegúrate de que planilla_id es la clave correcta
            
            // Invalida la caché para todas las páginas del periodo asociado
            for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                Cache::forget("planillas_periodo_{$detallePlanilla->planilla->periodo}_page_{$i}");
            }
        });
    
        // Invalida la caché al actualizar
        static::updated(function ($detallePlanilla) {
            // Invalida la caché de la planilla asociada
            Cache::forget("planilla_{$detallePlanilla->planilla_id}");
            
            // Invalida la caché para todas las páginas del periodo asociado
            for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                Cache::forget("planillas_periodo_{$detallePlanilla->planilla->periodo}_page_{$i}");
            }
        });
    
        // Invalida la caché al eliminar
        static::deleted(function ($detallePlanilla) {
            // Invalida la caché de la planilla asociada
            Cache::forget("planilla_{$detallePlanilla->planilla_id}");
            
            // Invalida la caché para todas las páginas del periodo asociado
            for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                Cache::forget("planillas_periodo_{$detallePlanilla->planilla->periodo}_page_{$i}");
            }
        });
    }
    
    
}
