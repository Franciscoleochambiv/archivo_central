<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RegistroDocumental extends Model
{
    use HasFactory;
    protected $table = 'registros_documentales';
    protected $fillable = [
        'oficina_id', 'entidad_id', 'nombre_entidad', 'titular', 'periodo',
        'anio_elaboracion', 'seccion', 'fechas_extremos', 'item', 'nro_archivo',
        'unidad_conservacion', 'serie_documental', 'nro_comprobantes',
        'ubicacion_estante', 'valor_serie_documental', 'folios', 'soporte_papel',
        'es_copia_original', 'anio_extremo_inicio', 'anio_extremo_fin',
        'color', 'observaciones', 'estado_archivador', 'ubicacion_actual'
    ];



      protected static function boot()
    {
        parent::boot();

        static::saved(function ($oficina) {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "rdocumental_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) { // Cambia 10 por el número máximo de páginas que esperas
                    Cache::forget("rdocumental_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });

        static::deleted(function ($oficina) {
            $searchTerm = request()->input('q');
            // Elimina caché para todas las páginas si hay un término de búsqueda
            if ($searchTerm) {
                $cacheKey = "rdocumental_search_" . md5($searchTerm);
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("{$cacheKey}_page_{$i}");
                }
            } else {
                // Elimina caché para todas las páginas si no hay término de búsqueda
                for ($i = 1; $i <= 10; $i++) {
                    Cache::forget("rdocumental_search_" . md5('') . "_page_{$i}"); // Maneja el caso sin término
                }
            }
        });
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoAdjunto::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function documentosAdjuntos()
{
    return $this->hasMany(DocumentoAdjunto::class, 'registro_documental_id');
}

}
