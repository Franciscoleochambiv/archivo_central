<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon; // Importar Carbon para manejo de fechas y horas
use App\Models\Asistencia;

use Illuminate\Support\Facades\Log; // Importar la clase Log para depuración


class AsistenciaController extends Controller
{
    protected $campos = ['morning_in', 'lunch_out', 'afternoon_in', 'afternoon_out'];
    private $etiquetas = [
        'morning_in' => 'Entrada',
        'lunch_out' => 'Salida a almuerzo',
        'afternoon_in' => 'Regreso de almuerzo',
        'afternoon_out' => 'Salida final'
    ];

    public function store_300(Request $request)
{
    try {
        $validated = $request->validate([
            'androidId' => 'required|string',
            'device_user_id' => 'required|integer',
            'fecha' => 'required|date',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validación fallida.',
            'errors' => $e->errors()
        ], 422);
    }

    try {
        $personal = DB::table('personal')
            ->where('device_user_id', $validated['device_user_id'])
            ->where('android_id', $validated['androidId'])
            ->first();

        if (!$personal) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajador no encontrado.',
            ], 404);
        }

        $fueraDeRango = false;
        $distancia = null;

        if ($personal->obra_id && $request->filled(['latitud', 'longitud'])) {
            $obra = DB::table('obras')->find($personal->obra_id);

            if ($obra) {
                $distancia = $this->calcularDistancia(
                    $obra->latitud,
                    $obra->longitud,
                    $validated['latitud'],
                    $validated['longitud']
                );

                if ($distancia > $obra->radio) {
                    $fueraDeRango = true;
                }
            }
        }

        // Hora actual del servidor
        $horaActual = now();
        $horaStr = $horaActual->format('H:i:s');
        $fechaStr = $horaActual->format('Y-m-d');

        // Obtener o crear el registro del día
        $asistencia = Asistencia::firstOrNew([
            'device_user_id' => $validated['device_user_id'],
            'fecha' => $validated['fecha']
        ]);

        // Verificar marca reciente (menos de 1 minuto)
        $ultimaMarca = collect([
            $asistencia->morning_in,
            $asistencia->lunch_out,
            $asistencia->afternoon_in,
            $asistencia->afternoon_out
        ])->filter()->map(function ($h) use ($fechaStr) {
            return \Carbon\Carbon::parse("$fechaStr $h");
        })->sortDesc()->first();

        if ($ultimaMarca && $horaActual->diffInMinutes($ultimaMarca) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Ya se registró una marca recientemente. Intenta en unos minutos.',
            ], 429);
        }

        // Determinar periodo según hora y campos vacíos
        $periodo = $this->determinarPeriodo($asistencia, $horaActual);

        if (!$periodo) {
            return response()->json([
                'success' => false,
                'message' => 'Ya se han registrado todas las marcas posibles hoy.'
            ], 400);
        }

        if ($fueraDeRango) {
            $asistencia->{$periodo . '_out_of_range'} = 1;
            $asistencia->{$periodo} = null;
        } else {
            $asistencia->{$periodo} = $horaStr;
            $asistencia->{$periodo . '_out_of_range'} = 0;
        }

        $asistencia->latitud = $validated['latitud'];
        $asistencia->longitud = $validated['longitud'];
        $asistencia->save();

        return response()->json([
            'success' => true,
            'message' => $fueraDeRango
                ? "Fuera de rango registrado en '$periodo'. Distancia: {$distancia}m (límite: {$obra->radio}m)."
                : "Asistencia registrada correctamente en '$periodo'.",
            'campo_registrado' => $periodo,
            'data' => $asistencia,
            'distance' => $distancia,
            'allowed_radius' => $obra->radio ?? null
        ]);
    } catch (\Throwable $e) {
        Log::error('Error al registrar asistencia', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error inesperado al registrar la asistencia.',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function determinarPeriodo($asistencia, $hora)
{
    $horaInt = intval($hora->format('H'));

    $sinMarcas = is_null($asistencia->morning_in) &&
                 is_null($asistencia->lunch_out) &&
                 is_null($asistencia->afternoon_in) &&
                 is_null($asistencia->afternoon_out);

    if ($sinMarcas) {
        return 'morning_in'; // ✅ como en C#
    }

    if (is_null($asistencia->morning_in) && $horaInt <= 10)
        return 'morning_in';

    if (is_null($asistencia->lunch_out) && $horaInt >= 11 && $horaInt < 14)
        return 'lunch_out';

    if (is_null($asistencia->afternoon_in) && $horaInt >= 12 && $horaInt < 16)
        return 'afternoon_in';

    if (is_null($asistencia->afternoon_out) && $horaInt >= 16)
        return 'afternoon_out';

    return null; // ya se marcó todo
}




    public function store(Request $request)
{
    // Validar los datos recibidos
    try {

         // Formatear la hora antes de validar
         if ($request->has('hora')) {
            $hora = $request->input('hora');
            $request->merge([
                'hora' => \Carbon\Carbon::createFromFormat('H:i:s', $hora)->format('H:i:s'),
            ]);
        }

        $validated = $request->validate([
            'androidId' => 'required|string',
            'device_user_id' => 'required|integer',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i:s',
            'periodo' => 'required|in:morning_in,lunch_out,afternoon_in,afternoon_out',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validación fallida.',           
            'errors' => $e->errors()
        ], 422);
    }
    try {
        // Obtener el trabajador y su obra asignada
        //$personal = DB::table('personal')->where('device_user_id', $validated['device_user_id'])->first();

        $personal = DB::table('personal')
        ->where('device_user_id', $validated['device_user_id'])
        ->where('android_id', $validated['androidId']) // Filtrar también por androidId
        ->first();
        

        if (!$personal) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el trabajador con el ID proporcionado.',
            ], 404);
        }

        // Verificar si el trabajador está asignado a una obra
        $fueraDeRango = false;
        $distancia = null;
        
        //dd()

        //dd($personal->obra_id);

        if ($personal->obra_id) {
            $obra = DB::table('obras')->where('id', $personal->obra_id)->first();

            if ($obra && $request->filled(['latitud', 'longitud'])) {
                // Calcular la distancia usando Haversine
                $distancia = $this->calcularDistancia(
                    $obra->latitud,
                    $obra->longitud,
                    $validated['latitud'],
                    $validated['longitud']
                );

                // Verificar si está fuera del rango permitido
                if ($distancia > $obra->radio) {
                    $fueraDeRango = true;
                }
            }
        }

   

        // Guardar la asistencia
        $asistencia = Asistencia::firstOrNew([
            'device_user_id' => $validated['device_user_id'],
            'fecha' => $validated['fecha']
        ]);

        // Actualizar los campos correspondientes
        if ($fueraDeRango) {
            // Marcar el campo fuera de rango correspondiente al período
            $asistencia->{$validated['periodo'] . '_out_of_range'} = 1;
            $asistencia->{$validated['periodo']} = null;

        } else {
            // Registrar la hora solo si está dentro del rango permitido
            $asistencia->{$validated['periodo']} = $validated['hora'];
            $asistencia->{$validated['periodo'] . '_out_of_range'} = 0;
        }

        // Actualizar latitud y longitud
        $asistencia->latitud = $validated['latitud'];
        $asistencia->longitud = $validated['longitud'];

        // Guardar el registro
        $asistencia->save();

        // Devolver respuesta
        return response()->json([
            'success' => true,
            'message' => $fueraDeRango
                ? "Marcación registrada como fuera de rango para el período {$validated['periodo']}. Distancia calculada: {$distancia}m (permitido: {$obra->radio}m)."
                : 'Registro de asistencia guardado exitosamente.',
            'data' => $asistencia,
            'distance' => $distancia,
            'allowed_radius' => $obra->radio ?? null
        ], 200);

    } catch (\Throwable $e) {
        // Registrar el error
        Log::error('Error al guardar el registro de asistencia:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Devolver respuesta de error
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar el registro de asistencia.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Calcular la distancia entre dos puntos geográficos utilizando la fórmula de Haversine.
 *
 * @param float $lat1 Latitud del primer punto
 * @param float $lon1 Longitud del primer punto
 * @param float $lat2 Latitud del segundo punto
 * @param float $lon2 Longitud del segundo punto
 * @return float Distancia en metros
 */
private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
{
    $radioTierra = 6371000; // Radio de la Tierra en metros

    // Convertir grados a radianes
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    // Calcular diferencias
    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;

    // Aplicar fórmula de Haversine
    $a = sin($deltaLat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $radioTierra * $c;
}

    



    public function reporteTardanzas(Request $request)
    {
        // Recibir las fechas de inicio y fin desde la solicitud
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', '2025-01-31');
    
        // Recibir el término de búsqueda
        $searchTerm = $request->input('q', '');
    
        // Definir el número de registros por página
        $perPage = 10;
    
        // Construir la consulta base
        $query = DB::table('personal as p')
        ->select(
            'p.id as personal_id',
            DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
            DB::raw('COALESCE(SUM(d.descuento_morning_in), 0) + COALESCE(SUM(d.descuento_afternoon_in), 0) as total_descuento_tardanza'),
            'd.motivo'
        )
        ->leftJoin('descuentos_diarios as d', 'p.id', '=', 'd.personal_id')
        ->whereBetween('d.fecha', [$fechaInicio, $fechaFin])
        ->groupBy('p.id', 'p.nombres', 'p.apellidos', 'd.motivo')
        ->orderByDesc('total_descuento_tardanza');
    
    
        // Si hay un término de búsqueda, aplicar filtro
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        // Aplicar paginación
        $detalles = $query->orderBy('personal_id')->paginate($perPage);
    
        // Mapear los datos para la respuesta
        $data = $detalles->getCollection()->map(function ($detalle) {
            return [
                'personal_id'        => $detalle->personal_id,
                'nombre_completo'    => $detalle->nombre_completo,
                'total_descuento_tardanza' => $detalle->total_descuento_tardanza,
                'motivo'=> $detalle->motivo,
                
            ];
        });
    
        // Reemplazar los datos mapeados en el paginador
        $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $detalles->total(),
            $detalles->perPage(),
            $detalles->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    
        // Retornar la respuesta paginada como JSON
        return response()->json($paginado);
    }

    public function reporteTardanzast(Request $request)
    {
        // Recibir las fechas de inicio y fin desde la solicitud
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', '2025-01-31');
    
        // Recibir el término de búsqueda
        $searchTerm = $request->input('q', '');
    
        // Construir la consulta base
        $query = DB::table('personal as p')
            ->select(
                'p.id as personal_id',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
                DB::raw('COALESCE(SUM(d.descuento_morning_in), 0) + COALESCE(SUM(d.descuento_afternoon_in), 0) as total_descuento_tardanza'),
                'd.motivo'
            )
            ->leftJoin('descuentos_diarios as d', 'p.id', '=', 'd.personal_id')
            ->whereBetween('d.fecha', [$fechaInicio, $fechaFin])
            ->groupBy('p.id', 'p.nombres', 'p.apellidos', 'd.motivo')
            ->orderByDesc('total_descuento_tardanza');
    
        // Si hay un término de búsqueda, aplicar filtro
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        // Obtener los resultados completos sin paginación
        $detalles = $query->get();
    
        // Mapear los datos para la respuesta
        $data = $detalles->map(function ($detalle) {
            return [
                'personal_id'           => $detalle->personal_id,
                'nombre_completo'       => $detalle->nombre_completo,
                'total_descuento_tardanza' => $detalle->total_descuento_tardanza,
                'motivo'                => $detalle->motivo,
            ];
        });
    
        // Retornar los datos como JSON
        return response()->json($data);
    }
    
    
    
    


    public function reporteSalidasAnticipadas(Request $request)
{
    // Recibir fechas de inicio y fin desde la solicitud
    $fechaInicio = $request->input('inicio', '2025-01-01');
    $fechaFin = $request->input('fin', '2025-01-31');

    // Recibir el término de búsqueda
    $searchTerm = $request->input('q', '');

    // Definir el número de registros por página
    $perPage = 10;

    // Construir la consulta base
    $query = DB::table('personal AS p')
        ->select(
            'p.id AS personal_id',
            DB::raw("CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo"),
            DB::raw('COALESCE(SUM(d.descuento_afternoon_out), 0) AS total_descuento_anticipadas'),
             'd.motivo'
        )
        ->leftJoin('descuentos_diarios AS d', 'p.id', '=', 'd.personal_id')
        ->whereBetween('d.fecha', [$fechaInicio, $fechaFin])
        ->where(function ($q) {
            $q->whereNotNull('d.descuento_morning_out')
              ->orWhereNotNull('d.descuento_afternoon_out');
        })
        ->groupBy('p.id', 'p.nombres', 'p.apellidos', 'd.motivo')
        ->having('total_descuento_anticipadas', '>', 0) // Excluir registros con 0 de total_descuento_anticipadas
        ->orderByDesc('total_descuento_anticipadas');

    // Si hay un término de búsqueda, aplicar filtro
    if (!empty($searchTerm)) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
              ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Aplicar paginación
    $detalles = $query->paginate($perPage);

    // Mapear los datos para la respuesta
    $data = $detalles->getCollection()->map(function ($detalle) {
        return [
            'personal_id'               => $detalle->personal_id,
            'nombre_completo'           => $detalle->nombre_completo,
            'total_descuento_anticipadas' => $detalle->total_descuento_anticipadas,
            'motivo'=> $detalle->motivo,
        ];
    });

    // Reemplazar los datos mapeados en el paginador
    $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,
        $detalles->total(),
        $detalles->perPage(),
        $detalles->currentPage(),
        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    );

    // Retornar la respuesta paginada como JSON
    return response()->json($paginado);
}

public function reporteSalidasAnticipadast(Request $request)
{
    // Recibir fechas de inicio y fin desde la solicitud
    $fechaInicio = $request->input('inicio', '2025-01-01');
    $fechaFin = $request->input('fin', '2025-01-31');

    // Recibir el término de búsqueda
    $searchTerm = $request->input('q', '');

    // Construir la consulta base
    $query = DB::table('personal AS p')
        ->select(
            'p.id AS personal_id',
            DB::raw("CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo"),
            DB::raw('COALESCE(SUM(d.descuento_afternoon_out), 0) AS total_descuento_anticipadas'),
            'd.motivo'
        )
        ->leftJoin('descuentos_diarios AS d', 'p.id', '=', 'd.personal_id')
        ->whereBetween('d.fecha', [$fechaInicio, $fechaFin])
        ->where(function ($q) {
            $q->whereNotNull('d.descuento_morning_out')
              ->orWhereNotNull('d.descuento_afternoon_out');
        })
        ->groupBy('p.id', 'p.nombres', 'p.apellidos', 'd.motivo')
        ->having('total_descuento_anticipadas', '>', 0) // Excluir registros con 0 de total_descuento_anticipadas
        ->orderByDesc('total_descuento_anticipadas');

    // Si hay un término de búsqueda, aplicar filtro
    if (!empty($searchTerm)) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
              ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Obtener los resultados completos
    $detalles = $query->get();

    // Mapear los datos para la respuesta
    $data = $detalles->map(function ($detalle) {
        return [
            'personal_id'               => $detalle->personal_id,
            'nombre_completo'           => $detalle->nombre_completo,
            'total_descuento_anticipadas' => $detalle->total_descuento_anticipadas,
            'motivo'                    => $detalle->motivo,
        ];
    });

    // Retornar la respuesta completa como JSON
    return response()->json($data);
}


public function reporteAsistenciaDiaria(Request $request)
{
    try {
        // Recibir fechas de inicio y fin desde la solicitud
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', '2025-01-31');

        // Recibir el término de búsqueda
        $searchTerm = $request->input('q', '');

        // Recibir el tipo de contrato
        $tipoContrato = $request->input('tipo_contrato', null); // Puede ser 1, 2 o null para todos

        // Parámetros para llegadas tarde
        $orderBy = $request->input('order_by', 'fecha');

        // Definir el número de registros por página
        $perPage = 10;

        // Construir la consulta base
        $query = DB::table('asistencia as a')
            ->select(
                'a.fecha',
                'a.device_user_id',
                'a.morning_in',
                'a.lunch_out',
                'a.afternoon_in',
                'a.afternoon_out',
                'a.latitud',
                'a.longitud',
                'a.morning_in_out_of_range',
                'a.lunch_out_out_of_range',
                'a.afternoon_in_out_of_range',
                'a.afternoon_out_out_of_range',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
                'p.tipo_contrato'
            )
            ->leftJoin('personal as p', 'a.device_user_id', '=', 'p.device_user_id')
            ->whereBetween('a.fecha', [$fechaInicio, $fechaFin]);

        // Aplicar filtro por tipo de contrato si está definido
        if (!is_null($tipoContrato)) {
            $query->where('p.tipo_contrato', $tipoContrato);
        }

        
        
        // Si hay un término de búsqueda, aplicar filtro
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('a.device_user_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        $query->orderBy('a.fecha', 'asc'); // Orden cronológico


        if ($orderBy === 'morning_in') {
            $query->orderBy('a.morning_in', 'asc'); // Ordenar por hora de llegada en la mañana
        } elseif ($orderBy === 'afternoon_in') {
            $query->orderBy('a.afternoon_in', 'asc'); // Ordenar por hora de llegada en la tarde
        } else {
            // Orden predeterminado si no se envía `order_by`
            $query->orderBy('a.morning_in', 'asc')
                  ->orderBy('a.afternoon_in', 'asc');
        }
        
             

        // Aplicar paginación
        $detalles = $query->paginate($perPage);

        // Mapear los datos para la respuesta
        $data = $detalles->getCollection()->map(function ($detalle) {
            return [
                'fecha'             => $detalle->fecha,
                'device_user_id'    => $detalle->device_user_id,
                'nombre_completo'   => $detalle->nombre_completo,
                'morning_in'        => $detalle->morning_in,
                'lunch_out'         => $detalle->lunch_out,
                'afternoon_in'      => $detalle->afternoon_in,
                'afternoon_out'     => $detalle->afternoon_out,
                'latitud'           => $detalle->latitud,
                'longitud'          => $detalle->longitud,
                'morning_in_out_of_range' => $detalle->morning_in_out_of_range,
                'lunch_out_out_of_range'  => $detalle->lunch_out_out_of_range,
                'afternoon_in_out_of_range' => $detalle->afternoon_in_out_of_range,
                'afternoon_out_out_of_range' => $detalle->afternoon_out_out_of_range,
            ];
        });

        // Reemplazar los datos mapeados en el paginador
        $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $detalles->total(),
            $detalles->perPage(),
            $detalles->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Retornar la respuesta paginada como JSON
        return response()->json($paginado);

    } catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}






public function reporteAsistenciaDiaria_VO(Request $request)
{
    try {
        // Recibir fechas de inicio y fin desde la solicitud
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', '2025-01-31');

        // Recibir el término de búsqueda
        $searchTerm = $request->input('q', '');

        // Definir el número de registros por página
        $perPage = 10;

        // Construir la consulta base
        $query = DB::table('asistencia as a')
            ->select(
                'a.fecha',
                'a.device_user_id',
                'a.morning_in',
                'a.lunch_out',
                'a.afternoon_in',
                'a.afternoon_out',
                'a.latitud',
                'a.longitud',
                'a.morning_in_out_of_range',
                'a.lunch_out_out_of_range',
                'a.afternoon_in_out_of_range',
                'a.afternoon_out_out_of_range',
               
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo")
            )
            ->leftJoin('personal as p', 'a.device_user_id', '=', 'p.device_user_id')
            ->whereBetween('a.fecha', [$fechaInicio, $fechaFin])
            ->orderBy('a.morning_in')
            ->orderBy('a.afternoon_in');

        // Si hay un término de búsqueda, aplicar filtro
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('a.device_user_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Aplicar paginación
        $detalles = $query->orderBy('a.fecha', 'desc')->paginate($perPage);

        // Mapear los datos para la respuesta
        $data = $detalles->getCollection()->map(function ($detalle) {
            return [
                'fecha'             => $detalle->fecha,
                'device_user_id'    => $detalle->device_user_id,
                'nombre_completo'   => $detalle->nombre_completo,
                'morning_in'      => $detalle->morning_in,
                'lunch_out'       => $detalle->lunch_out,
                'afternoon_in'      => $detalle->afternoon_in,
                'afternoon_out'       => $detalle->afternoon_out,
                
                'latitud' => $detalle->latitud,
                'longitud' => $detalle->longitud,
                'morning_in_out_of_range' => $detalle->morning_in_out_of_range,
                'lunch_out_out_of_range' => $detalle->lunch_out_out_of_range,
                'afternoon_in_out_of_range' => $detalle->afternoon_in_out_of_range,
                'afternoon_out_out_of_range' => $detalle->afternoon_out_out_of_range,
              
                
            ];
        });

        // Reemplazar los datos mapeados en el paginador
        $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $detalles->total(),
            $detalles->perPage(),
            $detalles->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Retornar la respuesta paginada como JSON
        return response()->json($paginado);

    } catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function reporteAsistenciaDiariat(Request $request)
{
    try {
        // Recibir fechas de inicio y fin desde la solicitud
        $fechaInicio = $request->input('inicio', '2025-01-01');
        $fechaFin = $request->input('fin', '2025-01-31');

        // Recibir el término de búsqueda
        $searchTerm = $request->input('q', '');

          // Recibir el tipo de contrato
        $tipoContrato = $request->input('tipo_contrato', null); // Puede ser 1, 2 o null para todos

          // Parámetros para llegadas tarde
        $orderBy = $request->input('order_by', 'fecha');
  

        // Construir la consulta base
        $query = DB::table('asistencia as a')
            ->select(
                'a.fecha',
                'a.device_user_id',
                'a.morning_in',
                'a.lunch_out',
                'a.afternoon_in',
                'a.afternoon_out',
                'a.latitud',
                'a.longitud',
                'a.morning_in_out_of_range',
                'a.lunch_out_out_of_range',
                'a.afternoon_in_out_of_range',
                'a.afternoon_out_out_of_range',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
                'p.tipo_contrato'
            )
            ->leftJoin('personal as p', 'a.device_user_id', '=', 'p.device_user_id')
            ->whereBetween('a.fecha', [$fechaInicio, $fechaFin]);

             // Aplicar filtro por tipo de contrato si está definido
        if (!is_null($tipoContrato)) {
            $query->where('p.tipo_contrato', $tipoContrato);
        }
          
          

        // Si hay un término de búsqueda, aplicar filtro
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('p.nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.apellidos', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('a.device_user_id', 'LIKE', "%{$searchTerm}%");
            });
        }

       // Siempre ordenar primero por fecha
        $query->orderBy('a.fecha', 'asc');

        // Aplicar lógica de orden según el parámetro `order_by`
        if ($orderBy === 'morning_in') {
            $query->orderBy('a.morning_in', 'asc'); // Ordenar por hora de llegada en la mañana
        } elseif ($orderBy === 'afternoon_in') {
            $query->orderBy('a.afternoon_in', 'asc'); // Ordenar por hora de llegada en la tarde
        } else {
            // Orden predeterminado si no se envía `order_by`
            $query->orderBy('a.morning_in', 'asc')
                ->orderBy('a.afternoon_in', 'asc');
        }

        // Obtener todos los resultados sin paginación
        $detalles = $query->get();

        // Mapear los datos para la respuesta
        $data = $detalles->map(function ($detalle) {
            return [
                'fecha'           => $detalle->fecha,
                'device_user_id'  => $detalle->device_user_id,
                'nombre_completo' => $detalle->nombre_completo,
                'morning_in'      => $detalle->morning_in,
                'lunch_out'       => $detalle->lunch_out,
                'afternoon_in'    => $detalle->afternoon_in,
                'afternoon_out'   => $detalle->afternoon_out,

                'latitud' => $detalle->latitud,
                'longitud' => $detalle->longitud,
                'morning_in_out_of_range' => $detalle->morning_in_out_of_range,
                'lunch_out_out_of_range' => $detalle->lunch_out_out_of_range,
                'afternoon_in_out_of_range' => $detalle->afternoon_in_out_of_range,
                'afternoon_out_out_of_range' => $detalle->afternoon_out_out_of_range,
            ];
        });

        // Retornar los datos como JSON
        return response()->json($data);

    } catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
  

    

    

    public function reporteMensual(Request $request)
{
    // Definir el número de registros por página
    $perPage = 10;

    // Obtener el mes desde la solicitud, o usar el mes actual por defecto
    $mes = $request->input('mes', Carbon::now()->format('F'));

    // Obtener el término de búsqueda desde la solicitud
    $searchTerm = $request->input('q', '');

    // Construir la consulta base
    $query = \App\Models\DetalleAsistencia::with('personal')
        ->where('mes', $mes);

    // Si hay un término de búsqueda, aplicar un filtro
    if (!empty($searchTerm)) {
        $query->whereHas('personal', function ($query) use ($searchTerm) {
            $query->where('nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('apellidos', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Aplicar paginación
    $detalles = $query->orderBy('personal_id')->paginate($perPage);

    // Mapear los datos para la respuesta
    $data = $detalles->getCollection()->map(function ($detalle) {
        return [
            'mes'                => $detalle->mes,
            'personal_id'        => $detalle->personal_id,
            'nombre_completo'    => $detalle->personal->nombres . ' ' . $detalle->personal->apellidos,
            'dias_habiles'       => $detalle->dias_habiles,
            'dias_trabajados'    => $detalle->dias_trabajados,
            'descuento_aplicado' => $detalle->descuento_aplicado,
            'remuneracion_final' => $detalle->remuneracion_final,
        ];
    });

    // Reemplazar los datos mapeados en el paginador
    $paginado = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,
        $detalles->total(),
        $detalles->perPage(),
        $detalles->currentPage(),
        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    );

    // Retornar la respuesta paginada como JSON
    return response()->json($paginado);
}

    
public function reporteMensualt(Request $request)
{
    try {
        // Obtener el mes desde la solicitud, o usar el mes actual por defecto
        $mes = $request->input('mes', Carbon::now()->format('F'));

        // Obtener el término de búsqueda desde la solicitud
        $searchTerm = $request->input('q', '');

        // Construir la consulta base
        $query = \App\Models\DetalleAsistencia::with('personal')
            ->where('mes', $mes);

        // Si hay un término de búsqueda, aplicar un filtro
        if (!empty($searchTerm)) {
            $query->whereHas('personal', function ($q) use ($searchTerm) {
                $q->where('nombres', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('apellidos', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Obtener todos los resultados sin paginación
        $detalles = $query->orderBy('personal_id')->get();

        // Mapear los datos para la respuesta
        $data = $detalles->map(function ($detalle) {
            return [
                'mes'                => $detalle->mes,
                'personal_id'        => $detalle->personal_id,
                'nombre_completo'    => $detalle->personal->nombres . ' ' . $detalle->personal->apellidos,
                'dias_habiles'       => $detalle->dias_habiles,
                'dias_trabajados'    => $detalle->dias_trabajados,
                'descuento_aplicado' => $detalle->descuento_aplicado,
                'remuneracion_mensual'=> $detalle->personal->remuneracion_mensual,
                'remuneracion_final' => $detalle->remuneracion_final,
            ];
        });

        // Retornar la respuesta como JSON
        return response()->json($data);

    } catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


public function calcularSueldo(Request $request)
{
    try {
        // Obtener el mes y el DNI desde la solicitud
        $mes = $request->input('mes', Carbon::now()->format('F'));
        $dni = $request->input('dni', '');

        // Validar que el DNI haya sido proporcionado
        if (empty($dni)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debe proporcionar un DNI para la consulta.',
            ], 400);
        }

        // Construir la consulta base
        $query = \App\Models\DetalleAsistencia::with('personal')
            ->where('mes', $mes)
            ->whereHas('personal', function ($q) use ($dni) {
                $q->where('dni', $dni);
            });

        // Obtener los resultados
        $detalles = $query->orderBy('personal_id')->get();

        // Verificar si hay resultados
        if ($detalles->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron registros para el mes y DNI proporcionados.',
            ], 404);
        }

        // Mapear los datos para la respuesta
        $data = $detalles->map(function ($detalle) {
            // Calcular la remuneración final
            //$remuneracionFinal = $detalle->remuneracion_mensual - $detalle->descuento_aplicado;

            return [
                'mes'                => $detalle->mes,
                'personal_id'        => $detalle->personal_id,
                'nombre_completo'    => $detalle->personal->nombres . ' ' . $detalle->personal->apellidos,
                'dni'                => $detalle->personal->dni,
                'dias_habiles'       => $detalle->dias_habiles,
                'dias_trabajados'    => $detalle->dias_trabajados,
                'descuento_aplicado' => $detalle->descuento_aplicado,
                'remuneracion_mensual'=> $detalle->personal->remuneracion_mensual,
                'remuneracion_final' =>  $detalle->remuneracion_final,
            ];
        });

        // Retornar la respuesta como JSON
        return response()->json($data);

    } catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    

    public function procesarAsistencia()
    {

      try{
        // Obtener los porcentajes de descuento desde .env (o config)
        // Ajusta los nombres de las variables .env según necesites
        //$tardanza_11_30 = env('TARDANZA_11_30', 0.02);
        //$tardanza_31_60 = env('TARDANZA_31_60', 0.05);
        //$tardanza_mas_60 = env('TARDANZA_MAS_60', 0.10);

        // Obtener la primera configuración de la tabla "configuracion"
       $config = DB::table('configuracion')->first();

       // Usar valores por defecto si no hay registros en la tabla
       $tardanza_11_30 = $config->tardanza_11_30 ?? 0.02;
       $tardanza_31_60 = $config->tardanza_31_60 ?? 0.05;
       $tardanza_mas_60 = $config->tardanza_mas_60 ?? 0.10;



        // Obtener todos los empleados
        $empleados = DB::table('personal')->get();

        // Mes actual, usado para detalle_asistencia
        $mesActual = Carbon::now()->format('F'); // Ej: "January", "February", etc.

        foreach ($empleados as $empleado) {
            // Calcular días hábiles para el mes actual
            $diasHabiles = 30;
            //$this->calcularDiasHabiles(Carbon::now()->month);

            // Obtener las asistencias del empleado en el mes en curso
            $asistencias = DB::table('asistencia')
                ->where('device_user_id', $empleado->device_user_id)
                ->whereMonth('fecha', Carbon::now()->month)
                ->get();

            // Acumulador de descuentos totales
            $descuentoTotal = 0;

            foreach ($asistencias as $asistencia) {
                // Calcula los 4 posibles descuentos de un día
                $descuentoDia = $this->calcularDescuentoPorDia(
                    $asistencia,
                    $empleado->remuneracion_mensual / $diasHabiles,
                    $tardanza_11_30,
                    $tardanza_31_60,
                    $tardanza_mas_60
                );

                // Sumar al total de descuentos del mes
                $descuentoTotal += $descuentoDia['total'];
                

                // Registrar (o actualizar) los descuentos diarios
                DB::table('descuentos_diarios')->updateOrInsert(
                    [
                        'personal_id' => $empleado->id,
                        'fecha'       => $asistencia->fecha,
                    ],
                    [
                        // Nuevas columnas para cada tipo de descuento
                        'descuento_morning_in'    => $descuentoDia['morning_in'],
                        'descuento_morning_out'   => $descuentoDia['morning_out'],
                        'descuento_afternoon_in'  => $descuentoDia['afternoon_in'],
                        'descuento_afternoon_out' => $descuentoDia['afternoon_out'],
                        // Si quieres seguir manteniendo la “suma” en morning/afternoon antiguos, podrías hacer:
                        // 'descuento_morning'   => $descuentoDia['morning_in'] + $descuentoDia['morning_out'],
                        // 'descuento_afternoon' => $descuentoDia['afternoon_in'] + $descuentoDia['afternoon_out'],

                        'motivo'     => $descuentoDia['motivo'],
                        'updated_at' => now()
                    ]
                );
            }

            // Calcular la remuneración final después de todos los descuentos acumulados en el mes
            //$remuneracionFinal = $empleado->remuneracion_mensual - $descuentoTotal;

            // Obtener la remuneración diaria
            $remuneracionDiaria = $empleado->remuneracion_mensual / $diasHabiles;

            // Calcular la remuneración prorrateada según los días trabajados
            $remuneracionProrrateada = $remuneracionDiaria *  count($asistencias);

            // Calcular la remuneración final después de los descuentos
            $remuneracionFinal = $remuneracionProrrateada - $descuentoTotal;

          // dd($remuneracionDiaria);
            //revisar

            // Registrar (o actualizar) el detalle de asistencia/resumen del mes
            DB::table('detalle_asistencia')->updateOrInsert(
                [
                    'personal_id' => $empleado->id,
                    'mes'         => $mesActual
                ],
                [
                    'dias_habiles'       => $diasHabiles,
                    'dias_trabajados'    => count($asistencias),
                    'descuento_aplicado' => $descuentoTotal,
                    'remuneracion_final' => $remuneracionFinal,
                    'updated_at'         => now()
                ]
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Asistencia procesada y descuentos aplicados.'
        ]);
    }
   catch (\Exception $e) {
        // Capturar errores y retornar un mensaje de error
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
    
   }

    /**
     * Calcula la cantidad de días hábiles (Lunes a Viernes) en un mes dado.
     */
    private function calcularDiasHabiles($mes)
    {
        $fechaInicio = Carbon::createFromDate(null, $mes, 1);
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        $diasHabiles = 0;

        while ($fechaInicio <= $fechaFin) {
            // dayOfWeek: Lunes=1, Martes=2, ... Domingo=0
            // En este caso, asumimos Lunes=1 a Viernes=5 (aunque Carbon usa Domingo=0, Lunes=1, etc.)
            if ($fechaInicio->dayOfWeek >= 0 && $fechaInicio->dayOfWeek <= 4) {
                $diasHabiles++;
            }
            $fechaInicio->addDay();
        }

        return $diasHabiles;
    }

    /**
     * Calcula los descuentos de un día por cada tramo horario:
     *  - Tardanza en la mañana (morning_in)
     *  - Salida anticipada a almorzar (lunch_out -> morning_out)
     *  - Tardanza al regresar del almuerzo (afternoon_in)
     *  - Salida anticipada en la tarde (afternoon_out)
     */
    private function calcularDescuentoPorDia($asistencia, $remuneracionDiaria, $tardanza_11_30, $tardanza_31_60, $tardanza_mas_60)
    {
        // Variables de descuento por cada bloque
        $descuentoMorningIn = 0;    // Tardanza mañana
        $descuentoMorningOut = 0;   // Salida antes de hora de almuerzo
        $descuentoAfternoonIn = 0;  // Tardanza al regresar de almuerzo
        $descuentoAfternoonOut = 0; // Salida antes de hora en la tarde
        $motivo = '';

        /**
         * 1. Tardanza en la mañana
         *    Hora permitida: 08:00:00
         */
        if ($asistencia->morning_in) {
            $horaEntrada = strtotime($asistencia->morning_in);
            $horaPermitida = strtotime('08:00:00');
            $diferenciaMinutos = ($horaEntrada - $horaPermitida) / 60; // min > 0 tardanza

            if ($diferenciaMinutos > 10 && $diferenciaMinutos <= 30) {
                $descuentoMorningIn = $remuneracionDiaria * $tardanza_11_30;
                $motivo .= 'Tardanza leve en la mañana; ';
            } elseif ($diferenciaMinutos > 30 && $diferenciaMinutos <= 60) {
                $descuentoMorningIn = $remuneracionDiaria * $tardanza_31_60;
                $motivo .= 'Tardanza moderada en la mañana; ';
            } elseif ($diferenciaMinutos > 60) {
                $descuentoMorningIn = $remuneracionDiaria * $tardanza_mas_60;
                $motivo .= 'Tardanza grave en la mañana; ';
            }
        }

        /**
         * 2. Salida anticipada para el almuerzo (lunch_out)
         *    Ejemplo: se permite salir a las 12:30, si sale antes => salida anticipada
         */
        if ($asistencia->lunch_out) {
            $horaSalidaAlmuerzo = strtotime($asistencia->lunch_out);
            $horaPermitidaAlmuerzo = strtotime('12:30:00');
            $diferenciaMinutos = ($horaPermitidaAlmuerzo - $horaSalidaAlmuerzo) / 60; 
            // Si difference > 0, salió antes

            if ($diferenciaMinutos > 0 && $diferenciaMinutos <= 30) {
                $descuentoMorningOut = $remuneracionDiaria * $tardanza_11_30;
                $motivo .= 'Salida anticipada leve antes del almuerzo; ';
            } elseif ($diferenciaMinutos > 30 && $diferenciaMinutos <= 60) {
                $descuentoMorningOut = $remuneracionDiaria * $tardanza_31_60;
                $motivo .= 'Salida anticipada moderada antes del almuerzo; ';
            } elseif ($diferenciaMinutos > 60) {
                $descuentoMorningOut = $remuneracionDiaria * $tardanza_mas_60;
                $motivo .= 'Salida anticipada grave antes del almuerzo; ';
            }
        }

        /**
         * 3. Tardanza al ingresar en la tarde (afternoon_in)
         *    Ejemplo: se debe volver a las 14:00, si llega después => tardanza
         */
        if ($asistencia->afternoon_in) {
            $horaEntradaTarde = strtotime($asistencia->afternoon_in);
            $horaPermitidaTarde = strtotime('14:00:00');
            $diferenciaMinutos = ($horaEntradaTarde - $horaPermitidaTarde) / 60;

            if ($diferenciaMinutos > 0 && $diferenciaMinutos <= 30) {
                $descuentoAfternoonIn = $remuneracionDiaria * $tardanza_11_30;
                $motivo .= 'Tardanza leve al regresar de almuerzo; ';
            } elseif ($diferenciaMinutos > 30 && $diferenciaMinutos <= 60) {
                $descuentoAfternoonIn = $remuneracionDiaria * $tardanza_31_60;
                $motivo .= 'Tardanza moderada al regresar de almuerzo; ';
            } elseif ($diferenciaMinutos > 60) {
                $descuentoAfternoonIn = $remuneracionDiaria * $tardanza_mas_60;
                $motivo .= 'Tardanza grave al regresar de almuerzo; ';
            }
        }

        /**
         * 4. Salida anticipada en la tarde (afternoon_out)
         *    Hora permitida de salida: 17:30:00
         */
        if ($asistencia->afternoon_out) {
            $horaSalida = strtotime($asistencia->afternoon_out);
            $horaPermitida = strtotime('17:30:00');
            $diferenciaMinutos = ($horaPermitida - $horaSalida) / 60;
            // difference > 0 => salió antes

            if ($diferenciaMinutos > 0 && $diferenciaMinutos <= 30) {
                $descuentoAfternoonOut = $remuneracionDiaria * $tardanza_11_30;
                $motivo .= 'Salida anticipada leve en la tarde; ';
            } elseif ($diferenciaMinutos > 30 && $diferenciaMinutos <= 60) {
                $descuentoAfternoonOut = $remuneracionDiaria * $tardanza_31_60;
                $motivo .= 'Salida anticipada moderada en la tarde; ';
            } elseif ($diferenciaMinutos > 60) {
                $descuentoAfternoonOut = $remuneracionDiaria * $tardanza_mas_60;
                $motivo .= 'Salida anticipada grave en la tarde; ';
            }
        }

        // Suma total de los 4 descuentos
        $totalDescuento = $descuentoMorningIn + $descuentoMorningOut
                        + $descuentoAfternoonIn + $descuentoAfternoonOut;

        return [
            'morning_in'       => $descuentoMorningIn,
            'morning_out'      => $descuentoMorningOut,
            'afternoon_in'     => $descuentoAfternoonIn,
            'afternoon_out'    => $descuentoAfternoonOut,
            'total'            => $totalDescuento,
            'motivo'           => $motivo
        ];
    }


 public function registrar(Request $request)
{
    $validated = $request->validate([
        'device_user_id' => 'required|integer'
    ]);

    $fecha = Carbon::now()->toDateString();
    $hora = Carbon::now()->format('H:i:s');
    $ahora = Carbon::now();
    $deviceUserId = $validated['device_user_id'];

    // Asegura que el registro exista
    $asistencia = DB::table('asistencia')
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->first();

    if (!$asistencia) {
        DB::table('asistencia')->insert([
            'device_user_id' => $deviceUserId,
            'fecha' => $fecha
        ]);
        $asistencia = (object)[];
    }

    // Verifica marcas recientes
    foreach ($this->campos as $campo) {
        if (!empty($asistencia->$campo)) {
            $marca = Carbon::createFromTimeString($asistencia->$campo);
            if ($ahora->diffInMinutes($marca) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya realizaste una marca recientemente. Espera unos minutos antes de volver a marcar.'
                ], 429);
            }
        }
    }

    // Determinar campo por hora válida
    $periodo = $this->determinarPeriodoValido($asistencia, $ahora);

    if (!$periodo) {
        return response()->json([
            'success' => false,
            'message' => 'Ya se han registrado todas las marcas posibles hoy o no se puede determinar la siguiente marca.'
        ], 400);
    }

    // Actualizar la marca
    DB::table('asistencia')
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->update([
            $periodo => $hora
        ]);

    // Generar resumen actualizado
    $nuevoRegistro = DB::table('asistencia')
        ->select($this->campos)
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->first();

    $resumen = [];
    foreach ($this->campos as $campo) {
        $resumen[] = [
            'etiqueta' => $this->etiquetas[$campo],
            'hora' => !empty($nuevoRegistro->$campo) ? substr($nuevoRegistro->$campo, 0, 5) : '---'
        ];
    }

    return response()->json([
        'success' => true,
        'message' => "Asistencia registrada en '{$this->etiquetas[$periodo]}' con hora $hora.",
        'periodo' => $periodo,
        'hora' => $hora,
        'resumen' => $resumen
    ]);
}


    public function obtenerMarcas(Request $request)
    {
        $request->validate([
            'device_user_id' => 'required|integer',
            'fecha' => 'required|date'
        ]);

        $campos = ['morning_in', 'lunch_out', 'afternoon_in', 'afternoon_out'];

        $registro = DB::table('asistencia')
            ->select($campos)
            ->where('device_user_id', $request->device_user_id)
            ->where('fecha', $request->fecha)
            ->first();

        $marcas = [];

        if ($registro) {
            foreach ($campos as $campo) {
                if (!empty($registro->$campo)) {
                    $marcas[$campo] = $registro->$campo;
                }
            }
        }

        return response()->json([
            'success' => true,
            'marcas' => $marcas
        ]);
    }


    public function resumen(Request $request)
{
    $request->validate([
        'device_user_id' => 'required|integer',        
    ]);

    $fecha = Carbon::now()->toDateString(); // ✅ usa fecha del servidor

      //dd($request); 

    $campos = [
        'morning_in' => 'Entrada',
        'lunch_out' => 'Salida a almuerzo',
        'afternoon_in' => 'Regreso de almuerzo',
        'afternoon_out' => 'Salida final',
    ];

    $registro = DB::table('asistencia')
        ->select(array_keys($campos))
        ->where('device_user_id', $request->device_user_id)
        ->where('fecha', $fecha)
        ->first();

    //dd($registro);    

    if (!$registro) {
        return response()->json([
            'success' => true,
            'resumen' => collect($campos)->map(function ($etiqueta) {
                return [
                    'etiqueta' => $etiqueta,
                    'hora' => '---'
                ];
            })->values()
        ]);
    }

    $resumen = [];

    foreach ($campos as $campo => $etiqueta) {
        $resumen[] = [
            'etiqueta' => $etiqueta,
            'hora' => $registro->$campo ? substr($registro->$campo, 0, 5) : '---'
        ];
    }

    return response()->json([
        'success' => true,
        'resumen' => $resumen
    ]);
}



    private function determinarPeriodoValido($registro, $ahora)
    {
        $hora = $ahora->hour;

        if (!$registro || empty($registro->morning_in)) {
            if ($hora < 10) return 'morning_in';
        }

        if (empty($registro->lunch_out) && $hora >= 11 && $hora < 14) {
            return 'lunch_out';
        }

        if (empty($registro->afternoon_in) && $hora >= 12 && $hora < 16) {
            return 'afternoon_in';
        }

        if (empty($registro->afternoon_out) && $hora >= 16) {
            return 'afternoon_out';
        }

        return null;
    }


public function registrar_huellero(Request $request)
{
    $validated = $request->validate([
        'device_user_id' => 'required|integer',
        'fecha' => 'nullable|date',
        'hora' => 'nullable|date_format:H:i:s'
    ]);

    $fecha = $validated['fecha'] ?? Carbon::now()->toDateString();
    $hora = $validated['hora'] ?? Carbon::now()->format('H:i:s');
    $ahora = $validated['hora'] && $validated['fecha']
        ? Carbon::createFromFormat('Y-m-d H:i:s', "{$validated['fecha']} {$validated['hora']}")
        : Carbon::now();

    $deviceUserId = $validated['device_user_id'];

    // Asegura que el registro exista
    $asistencia = DB::table('asistencia')
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->first();

    if (!$asistencia) {
        DB::table('asistencia')->insert([
            'device_user_id' => $deviceUserId,
            'fecha' => $fecha
        ]);
        $asistencia = (object)[];
    }

    // Verifica marcas recientes
    foreach ($this->campos as $campo) {
        if (!empty($asistencia->$campo)) {
            $marca = Carbon::createFromTimeString($asistencia->$campo);
            if ($ahora->diffInMinutes($marca) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya realizaste una marca recientemente. Espera unos minutos antes de volver a marcar.'
                ], 429);
            }
        }
    }

    // Determinar campo por hora válida
    $periodo = $this->determinarPeriodoValido($asistencia, $ahora);

    if (!$periodo) {
        return response()->json([
            'success' => false,
            'message' => 'Ya se han registrado todas las marcas posibles hoy o no se puede determinar la siguiente marca.'
        ], 400);
    }

    // Actualizar la marca
    DB::table('asistencia')
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->update([
            $periodo => $hora
        ]);

    // Generar resumen actualizado
    $nuevoRegistro = DB::table('asistencia')
        ->select($this->campos)
        ->where('device_user_id', $deviceUserId)
        ->where('fecha', $fecha)
        ->first();

    $resumen = [];
    foreach ($this->campos as $campo) {
        $resumen[] = [
            'etiqueta' => $this->etiquetas[$campo],
            'hora' => !empty($nuevoRegistro->$campo) ? substr($nuevoRegistro->$campo, 0, 5) : '---'
        ];
    }

    return response()->json([
        'success' => true,
        'message' => "Asistencia registrada en '{$this->etiquetas[$periodo]}' con hora $hora.",
        'periodo' => $periodo,
        'hora' => $hora,
        'resumen' => $resumen
    ]);
}




public function reportePersonal(Request $request)
{
    try {
        // Validar los datos recibidos
        $request->validate([
            'dni' => 'required|string',
            'inicio' => 'required|date',
            'fin' => 'required|date',
        ]);

        $dni = $request->dni;
        $fechaInicio = $request->inicio;
        $fechaFin = $request->fin;
        $perPage = 10;

        // Consulta con filtros por fecha y DNI
        $query = DB::table('asistencia as a')
            ->select(
                'a.fecha',
                'a.device_user_id',
                'a.morning_in',
                'a.lunch_out',
                'a.afternoon_in',
                'a.afternoon_out',
                'a.latitud',
                'a.longitud',
                'a.morning_in_out_of_range',
                'a.lunch_out_out_of_range',
                'a.afternoon_in_out_of_range',
                'a.afternoon_out_out_of_range',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
                'p.tipo_contrato'
            )
            ->leftJoin('personal as p', 'a.device_user_id', '=', 'p.device_user_id')
            ->where('p.dni', $dni)
            ->whereBetween('a.fecha', [$fechaInicio, $fechaFin])
            ->orderBy('a.fecha', 'asc')
            ->orderBy('a.morning_in', 'asc')
            ->orderBy('a.afternoon_in', 'asc');

        // Ejecutar paginación
        $paginated = $query->paginate($perPage);

        // Mapeo de datos
        $result = $paginated->getCollection()->map(function ($item) {
            return [
                'fecha' => $item->fecha,
                'device_user_id' => $item->device_user_id,
                'nombre_completo' => $item->nombre_completo,
                'morning_in' => $item->morning_in,
                'lunch_out' => $item->lunch_out,
                'afternoon_in' => $item->afternoon_in,
                'afternoon_out' => $item->afternoon_out,
                'latitud' => $item->latitud,
                'longitud' => $item->longitud,
                'morning_in_out_of_range' => $item->morning_in_out_of_range,
                'lunch_out_out_of_range' => $item->lunch_out_out_of_range,
                'afternoon_in_out_of_range' => $item->afternoon_in_out_of_range,
                'afternoon_out_out_of_range' => $item->afternoon_out_out_of_range,
            ];
        });

        return response()->json([
            'data' => $result,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

//reportePersonal_excel
public function reportePersonal_excel(Request $request)
{
    try {
        $request->validate([
            'dni' => 'required|string',
            'inicio' => 'required|date',
            'fin' => 'required|date',
        ]);

        $dni = $request->dni;
        $fechaInicio = $request->inicio;
        $fechaFin = $request->fin;

        $datos = DB::table('asistencia as a')
            ->select(
                'a.fecha',
                'a.device_user_id',
                'a.morning_in',
                'a.lunch_out',
                'a.afternoon_in',
                'a.afternoon_out',
                'a.latitud',
                'a.longitud',
                'a.morning_in_out_of_range',
                'a.lunch_out_out_of_range',
                'a.afternoon_in_out_of_range',
                'a.afternoon_out_out_of_range',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo"),
                'p.tipo_contrato'
            )
            ->leftJoin('personal as p', 'a.device_user_id', '=', 'p.device_user_id')
            ->where('p.dni', $dni)
            ->whereBetween('a.fecha', [$fechaInicio, $fechaFin])
            ->orderBy('a.fecha', 'asc')
            ->orderBy('a.morning_in', 'asc')
            ->orderBy('a.afternoon_in', 'asc')
            ->get();

        // Formateo opcional
        $result = $datos->map(function ($item) {
            return [
                'fecha' => $item->fecha,
                'device_user_id' => $item->device_user_id,
                'nombre_completo' => $item->nombre_completo,
                'morning_in' => $item->morning_in,
                'lunch_out' => $item->lunch_out,
                'afternoon_in' => $item->afternoon_in,
                'afternoon_out' => $item->afternoon_out,
                'latitud' => $item->latitud,
                'longitud' => $item->longitud,
                'morning_in_out_of_range' => $item->morning_in_out_of_range,
                'lunch_out_out_of_range' => $item->lunch_out_out_of_range,
                'afternoon_in_out_of_range' => $item->afternoon_in_out_of_range,
                'afternoon_out_out_of_range' => $item->afternoon_out_out_of_range,
            ];
        });

        return response()->json($result);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}










}
