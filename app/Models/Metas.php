<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Log;

class Metas extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descripcion'
    ];

   
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($meta) {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "metas_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("metas_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });

        static::deleted(function ($meta) {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "metas_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("metas_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });
    }


   
}
