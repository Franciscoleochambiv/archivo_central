<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'latitud',
        'longitud',
        'radio',
        'direccion'
    ];

    /**
     * Relación con el modelo Personal
     */
    public function personal()
    {
        return $this->hasMany(Personal::class, 'obra_id');
    }

    /**
     * Relación con el modelo Asistencia
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'obra_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "obras_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("obras_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });

        static::deleted(function () {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "obras_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("obras_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });
    }
}
