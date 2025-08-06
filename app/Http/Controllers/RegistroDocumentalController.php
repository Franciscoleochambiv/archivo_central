<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroDocumental;
use App\Models\Entidad;


use App\Models\DocumentoAdjunto;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RegistroDocumentalController extends Controller
{
    //
    // Listado general



     public function index(Request $request)
    {
           
            $perPage = 10;

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Genera una clave de caché única basada en el término de búsqueda y la paginación
            $cacheKey = "rdocumental_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
            
            // Establece la duración del caché
            $cacheDuration = 60*480; // 60 minutos

            // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
            $oficinas = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
                // Construye la consulta base
                $query = RegistroDocumental::query()
                    ->orderBy('id', 'desc');

                // Si hay un término de búsqueda, agregar un filtro a la consulta
                if (strlen($searchTerm) >= 3) {
                    $query->where('ubicacion_estante', 'LIKE', "%{$searchTerm}%");
                }

                // Obtén los elementos paginados
                return $query->paginate($perPage);
            });



            // Revertir el orden de los elementos
            $data = $oficinas->items();
            //$data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $oficinas = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $oficinas->total(),
                $oficinas->perPage(),
                $oficinas->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $metas como JSON
            return response()->json($oficinas);
    }





    public function index2()
    {
        return RegistroDocumental::with(['oficina', 'entidad'])->get();
    }

   public function store(Request $request)
{
    try {
        $request->validate([
            'oficina_id' => 'required|exists:oficinas,id',
            'entidad_id' => 'required|exists:entidades,id',
            'periodo' => 'required|string',
            'anio_elaboracion' => 'required|digits:4',
            'seccion' => 'required|string',
            'fechas_extremos' => 'required|string',
            'nro_archivo' => 'required|string',
            'unidad_conservacion' => 'required|string',
            'serie_documental' => 'required|string',
            'nro_comprobantes' => 'required|string',
            'ubicacion_estante' => 'required|string',
            'valor_serie_documental' => 'required|in:T,P',
            'folios' => 'required|string',
            'soporte_papel' => 'required|string',
            'es_copia_original' => 'required|boolean',
            'anio_extremo_inicio' => 'required|string',
            'anio_extremo_fin' => 'required|string',
            'color' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado_archivador' => 'required|in:B,M,R',
            'ubicacion_actual' => 'required|in:A.C,PRESTAMO,DEVUELTO',
        ]);

        // Generar item automáticamente por oficina
        $ultimoItem = RegistroDocumental::where('oficina_id', $request->oficina_id)->max('item');
        $nuevoItem = $ultimoItem ? ((int) $ultimoItem + 1) : 1;

        // Preparar datos para guardar
        $datos = $request->all();
        $datos['item'] = $nuevoItem;

        // Guardar el registro documental
        $registro = RegistroDocumental::create($datos);

        // Guardar documentos adjuntos (si los hay)

        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $archivo) {
                $path = $archivo->store('documentos', 'public');

                DocumentoAdjunto::create([
                    'registro_documental_id' => $registro->id,
                    'nombre' => $archivo->getClientOriginalName(),
                    'ruta' => $path,
                ]);
            }
        }

        return response()->json([
            'mensaje' => 'Registro guardado correctamente',
            'registro_id' => $registro->id
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error al guardar expediente: ' . $e->getMessage());

        return response()->json([
            'mensaje' => 'Ocurrió un error al guardar el registro',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function conteo($id)
{
    $total = RegistroDocumental::where('oficina_id', $id)->count();

    return response()->json(['total' => $total]);
}


    // Búsqueda por filtros
    public function buscar(Request $request)
    {
        $query = RegistroDocumental::with(['oficina', 'entidad', 'documentos', 'movimientos']);
        //RegistroDocumental::query();

        if ($request->has('oficina_id') && $request->oficina_id != '') {
            $query->where('oficina_id', $request->oficina_id);
        }

        if ($request->has('serie') && $request->serie != '') {
            $query->where('serie_documental', 'like', '%' . $request->serie . '%');
        }

        if ($request->has('comprobante') && $request->comprobante != '') {
            $query->where('nro_comprobantes', $request->comprobante);
        }

        return $query->with(['oficina', 'entidad'])->get();
    }

    // Ver un expediente específico
    public function show($id)
    {
        return RegistroDocumental::with(['oficina', 'entidad', 'documentos', 'movimientos'])
            ->findOrFail($id);
    }


    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'oficina_id' => 'required|exists:oficinas,id',
            'entidad_id' => 'required|exists:entidades,id',
            'periodo' => 'required|string',
            'anio_elaboracion' => 'required|digits:4',
            'seccion' => 'required|string',
            'fechas_extremos' => 'required|string',
            'nro_archivo' => 'required|string',
            'unidad_conservacion' => 'required|string',
            'serie_documental' => 'required|string',
            'nro_comprobantes' => 'required|string',
            'ubicacion_estante' => 'required|string',
            'valor_serie_documental' => 'required|in:T,P',
            'folios' => 'required|string',
            'soporte_papel' => 'required|string',
            'es_copia_original' => 'required|boolean',
            'anio_extremo_inicio' => 'required|string',
            'anio_extremo_fin' => 'required|string',
            'color' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado_archivador' => 'required|in:B,M,R',
            'ubicacion_actual' => 'required|in:A.C,PRESTAMO,DEVUELTO',
        ]);

        $registro = RegistroDocumental::findOrFail($id);
        $registro->update($request->all());

        // Reemplazar archivos anteriores si hay nuevos
        if ($request->hasFile('documentos')) {
            foreach ($registro->documentos as $doc) {
                Storage::disk('public')->delete($doc->ruta);
                $doc->delete();
            }

            foreach ($request->file('documentos') as $archivo) {
                $path = $archivo->store('documentos', 'public');

                DocumentoAdjunto::create([
                    'registro_documental_id' => $registro->id,
                    'nombre' => $archivo->getClientOriginalName(),
                    'ruta' => $path,
                ]);
            }
        }

        return response()->json([
            'mensaje' => 'Registro actualizado correctamente',
            'registro_id' => $registro->id
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error al actualizar expediente: ' . $e->getMessage());

        return response()->json([
            'mensaje' => 'Ocurrió un error al actualizar el registro',
            'error' => $e->getMessage()
        ], 500);
    }
}

  public function destroy(Request $request)
     {
       
        $meta = RegistroDocumental::destroy($request->id);       
        return $meta;
                 
        
     }




   // Reporte general: todos los expedientes
    public function reporteExpedientes(Request $request)
    {
        $query = RegistroDocumental::with(['oficina', 'entidad']);
        if ($request->oficina_id) {
            $query->where('oficina_id', $request->oficina_id);
        }
        if ($request->entidad_id) {
            $query->where('entidad_id', $request->entidad_id);
        }
        return $query->get()->map->only([
            'id','item','nro_archivo','unidad_conservacion','serie_documental',
            'nro_comprobantes','ubicacion_estante','folios','es_copia_original',
            'anio_elaboracion','estado_archivador','ubicacion_actual'
        ]);
    }

    // Expedientes por Entidad
    public function reportePorEntidad(Request $request)
    {
        $query = RegistroDocumental::with('entidad');
        if ($request->entidad_id) {
            $query->where('entidad_id', $request->entidad_id);
        }
        return $query->get()->map(function ($r) {
            return [
                'entidad' => $r->entidad->nombre,
                'item' => $r->item,
                'serie_documental' => $r->serie_documental,
                'nro_comprobantes' => $r->nro_comprobantes,
                'anio_elaboracion' => $r->anio_elaboracion,
            ];
        });
    }

    // Expedientes por Oficina
    public function reportePorOficina(Request $request)
    {
        $query = RegistroDocumental::with('oficina');
        if ($request->oficina_id) {
            $query->where('oficina_id', $request->oficina_id);
        }
        return $query->get()->map(function ($r) {
            return [
                'oficina' => $r->oficina->nombre,
                'entidad' => $r->entidad->nombre,
                'periodo' => $r->periodo,
                'anio_elaboracion' => $r->anio_elaboracion,
                'seccion' => $r->seccion,
                'fechas_extremos' => $r->fechas_extremos,
                'item' => $r->item,
                'nro_archivo' => $r->nro_archivo,
                'unidad_conservacion' => $r->unidad_conservacion,
                'serie_documental' => $r->serie_documental,
                'nro_comprobantes' => $r->nro_comprobantes,
                'ubicacion_estante' => $r->ubicacion_estante,
                'valor_serie_documental' => $r->valor_serie_documental,
                'folios' => $r->folios,
                'soporte_papel' => $r->soporte_papel,
                'es_copia_original' => $r->es_copia_original ? 'Sí' : 'No',
                'anio_extremo_inicio' => $r->anio_extremo_inicio,
                'anio_extremo_fin' => $r->anio_extremo_fin,
                'color' => $r->color,
                'observaciones' => $r->observaciones,
                'estado_archivador' => $r->estado_archivador,
                'ubicacion_actual' => $r->ubicacion_actual,
       
            ];
        });
    }


    

    // Por Estado del Archivador
    public function reportePorEstadoArchivador(Request $request)
    {
        $query = RegistroDocumental::query();
        if ($request->estado_archivador) {
            $query->where('estado_archivador', $request->estado_archivador);
        }
        return $query->get()->map->only([
            'item','serie_documental','estado_archivador','ubicacion_actual','anio_elaboracion'
        ]);
    }

    // Por Ubicación Actual del expediente
    public function reportePorUbicacionActual(Request $request)
    {
        $query = RegistroDocumental::query();
        if ($request->ubicacion_actual) {
            $query->where('ubicacion_actual', $request->ubicacion_actual);
        }
        return $query->get()->map->only([
            'item','serie_documental','ubicacion_actual','estado_archivador','nro_comprobantes'
        ]);
    }  


}
