<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'personal';
    protected $primaryKey = 'id'; // Ajusta si tu PK es distinta
    
    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'device_user_id',
        'fecha_nacimiento',
        'dni',
        'spp',
        'r4',
        'r5',
        'idcargo',
        'fecha_ingreso',
        'idafp',
        'idonp',
        'remuneracion_mensual',
        'ip_address',
        'tipo_contrato',
        'mac_address',
        'obra_id',
        'android_id',

    ];
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

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_trabajador');
    }

    public function detalleplanilla()
    {
        return $this->hasMany(DetallePlanilla::class, 'personal_id');
    }


       // Relación con la tabla 'detalle_asistencia'
       public function detalleAsistencias()
       {
           return $this->hasMany(DetalleAsistencia::class);
       }
   
       // Relación con la tabla 'descuentos_diarios'
       public function descuentosDiarios()
       {
           return $this->hasMany(DescuentoDiario::class);
       }

         // Relación con DetalleAsistencia
    public function detallesAsistencia()
    {
        return $this->hasMany(DetalleAsistencia::class, 'personal_id');
    }

    public function asistencias()
    {
        // hasMany(<Modelo>, <FK en asistencia>, <PK en personal>)
        return $this->hasMany(Asistencia::class, 'device_user_id', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'device_user_id', 'device_user_id');
    }

    public function obra()
{
    return $this->belongsTo(Obra::class, 'obra_id');
}


       

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "personals_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("personals_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });

        static::deleted(function () {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "personals_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("personals_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });
    }






    

}
