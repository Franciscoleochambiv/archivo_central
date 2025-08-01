<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Planilla extends Model
{
    use HasFactory;

    protected $table = 'planillas';

    protected $fillable = [
        'anio','periodo','numero_planilla', 'tipo_planilla', 'fecha', 'remuneracion_bruta_total', 
        'observaciones','id_usuario',
        'ds311', 
        'ds313', 
        'descuentos_afp', 
        'descuentos_onp',  'descuentos_r4',
        'descuentos_r5', 'aportes_essalud', 'aportes_sctr', 'total_planilla'
    ];

    public function detallePlanillas()
    {
        return $this->hasMany(DetallePlanilla::class);
    }

    protected static function booted()
    {
        static::saved(function ($planilla) {
            // Invalida la caché para esta planilla cuando se guarda (creación o actualización)
            Cache::forget("planilla_{$planilla->id}");
            
            // Invalida la caché para las planillas por periodo
            $page = request()->get('page', 1); // Obtén el número de página actual
            for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                //Cache::forget("planillas_periodo_{$planilla->periodo}_page_{$i}");
                Cache::forget("planillas_periodo_{$planilla->periodo}_{$planilla->anio}_page_{$i}");

                
            }
        });
    
        static::deleted(function ($planilla) {
            // Invalida la caché para esta planilla cuando se elimina
            Cache::forget("planilla_{$planilla->id}");
            
            // Invalida la caché para las planillas por periodo
            $page = request()->get('page', 1); // Obtén el número de página actual
            for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                //Cache::forget("planillas_periodo_{$planilla->periodo}_page_{$i}");
                Cache::forget("planillas_periodo_{$planilla->periodo}_{$planilla->anio}_page_{$i}");

            }
        });
    }
    


    

}
