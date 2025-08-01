<?php

namespace App\Http\Controllers;

use App\Models\Planilla;
use App\Models\DetallePlanilla;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanillaController extends Controller
{
    public function index(Request $request)
    {
        // Define el número de elementos por página
        $perPage = 20;

        // Obtén el término de búsqueda desde la consulta
        $searchTerm = $request->input('q');

        // Construye la consulta base con eager loading para las relaciones  Personal::with(['cargo', 'afp', 'onp'])
        $query = Planilla::with(['detallePlanillas','personal'])
                         ->when(strlen($searchTerm) >= 3, function ($query) use ($searchTerm) {
                             $query->where('numero_planilla', 'LIKE', "%{$searchTerm}%");
                         })
                         ->orderBy('id', 'desc');
        

        // Obtén los elementos paginados
        $planillas = $query->paginate($perPage);

        // Revertir el orden de los elementos
        $data = $planillas->items();
       // $data = array_reverse($data);

        $planillas = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $planillas->total(),
            $planillas->perPage(),
            $planillas->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // Formatea los datos para incluir las relaciones
        $formattedData = $planillas->map(function ($planilla) {
            return [
                'id' => $planilla->id,
                'numero_planilla' => $planilla->numero_planilla,
                'fecha' => $planilla->fecha,
                'tipo_planilla' => $planilla->tipo_planilla,
                'remuneracion_bruta_total' => $planilla->remuneracion_bruta_total,
                'descuentos_afp' => $planilla->descuentos_afp,
                'descuentos_onp' => $planilla->descuentos_onp,
                'aportes_essalud' => $planilla->aportes_essalud,
                'aportes_sctr' => $planilla->aportes_sctr,
                'total_planilla' => $planilla->total_planilla,
                'detalle_planillas' => $planilla->detallePlanillas->map(function ($detalle) {
                    return [
                        'id' => $detalle->id,
                        'dni' => $detalle->dni,
                        'fecha_ingreso' => $detalle->fecha_ingreso,
                        'dias_trabajados' => $detalle->dias_trabajados,
                        'remuneracion_mensual' => $detalle->remuneracion_mensual,
                        'aguinaldo' => $detalle->aguinaldo,
                        'remuneracion_vacacional' => $detalle->remuneracion_vacacional,
                        'cts' => $detalle->cts,
                        'descuentos_afp_fondo' => $detalle->descuentos_afp_fondo,
                        'descuentos_afp_sfp_c' => $detalle->descuentos_afp_sfp_c,
                        'descuentos_afp_ps' => $detalle->descuentos_afp_ps,
                        'descuentos_onp' => $detalle->descuentos_onp,
                        'aportes_essalud' => $detalle->aportes_essalud,
                        'aportes_sctr' => $detalle->aportes_sctr,
                        'total_detalle' => $detalle->total_detalle,
                    ];
                })
            ];
        });

        // Retornar la variable $formattedData como JSON

        return response()->json($planillas);
        /*
        return response()->json([
            'data' => $formattedData,
            'pagination' => [
                'total' => $planillas->total(),
                'per_page' => $planillas->perPage(),
                'current_page' => $planillas->currentPage(),
                'last_page' => $planillas->lastPage(),
                'next_page_url' => $planillas->nextPageUrl(),
                'prev_page_url' => $planillas->previousPageUrl(),
            ]
        ]);
        */
    }


                public function show($id)
                {
                    $planilla = Planilla::with('detallePlanillas')->find($id);

                    if (!$planilla) {
                        return response()->json(['message' => 'Planilla no encontrada'], 404);
                    }

                    // Formatea los datos para incluir las relaciones
                    $formattedData = [
                        'id' => $planilla->id,
                        'numero_planilla' => $planilla->numero_planilla,
                        'periodo'=> $planilla->periodo,
                        'fecha' => $planilla->fecha,
                        'tipo_planilla' => $planilla->tipo_planilla,
                        'remuneracion_bruta_total' => $planilla->remuneracion_bruta_total,
                        'descuentos_afp' => $planilla->descuentos_afp,
                        'descuentos_onp' => $planilla->descuentos_onp,
                        'aportes_essalud' => $planilla->aportes_essalud,
                        'aportes_sctr' => $planilla->aportes_sctr,
                        'total_planilla' => $planilla->total_planilla,
                        'detalle_planillas' => $planilla->detallePlanillas->map(function ($detalle) {
                            return [
                                'id' => $detalle->id,
                                'dni' => $detalle->dni,
                                'fecha_ingreso' => $detalle->fecha_ingreso,
                                'dias_trabajados' => $detalle->dias_trabajados,
                                'remuneracion_mensual' => $detalle->remuneracion_mensual,
                                'aguinaldo' => $detalle->aguinaldo,
                                'remuneracion_vacacional' => $detalle->remuneracion_vacacional,
                                'cts' => $detalle->cts,
                                'descuentos_afp_fondo' => $detalle->descuentos_afp_fondo,
                                'descuentos_afp_sfp_c' => $detalle->descuentos_afp_sfp_c,
                                'descuentos_afp_ps' => $detalle->descuentos_afp_ps,
                                'descuentos_onp' => $detalle->descuentos_onp,
                                'aportes_essalud' => $detalle->aportes_essalud,
                                'aportes_sctr' => $detalle->aportes_sctr,
                                'total_detalle' => $detalle->total_detalle,
                            ];
                        })
                    ];

                    return response()->json($formattedData);
                }


            public function store(Request $request)
            {
                // Validar los datos de entrada
                $validatedData = $request->validate([
                    'selectedPeriodo' => 'required|integer',
                    'selectedTipo' => 'required|integer',
                    'meta' => 'required|integer',
                    'fechaActual' => 'required|date',
                    'nuevoArray' => 'required|array',
                    'nuevoArray.*.dni' => 'required|string',
                    'nuevoArray.*.dias_trabajados' => 'required|integer',
                    'nuevoArray.*.remuneracion_mensual' => 'required|numeric',
                    'nuevoArray.*.aguinaldo' => 'nullable|numeric',
                    'nuevoArray.*.remuneracion_vacacional' => 'nullable|numeric',
                    'nuevoArray.*.cts' => 'nullable|numeric',
                   
                ]);

                // Obtener los datos del formulario
                $periodo = $validatedData['selectedPeriodo'];
                $tipo_planilla = $validatedData['selectedTipo'];
                $numero_planilla = $validatedData['meta'];
                $fecha = $validatedData['fechaActual'];
                $personalDetails = $validatedData['nuevoArray'];


                    // Inicializar el planillaId como null
                $planillaId = null;

                // Iniciar la transacción
                DB::transaction(function () use ($numero_planilla, $tipo_planilla, $periodo,$fecha, $personalDetails, &$planillaId) {
                    // Insertar en la tabla planillas
                    $totalRemuneracionBruta =0;
                    $totalDescuentosAfp = 0;
                    $totalDescuentosOnp = 0;
                    $totalAportesEssalud = 0;
                    $totalAportesSctr = 0;
                    // Insertar en la tabla planillas (sin valores iniciales)
                    $planillaId = Planilla::create([
                        'periodo'=> $periodo,
                        'numero_planilla' => $numero_planilla,
                        'tipo_planilla' => $tipo_planilla,
                        'fecha' => $fecha,
                        'remuneracion_bruta_total' => 0,  // Se actualizará más tarde
                        'descuentos_afp' => 0,            // Se actualizará más tarde
                        'descuentos_onp' => 0,            // Se actualizará más tarde
                        'aportes_essalud' => 0,           // Se actualizará más tarde
                        'aportes_sctr' => 0,              // Se actualizará más tarde
                        'total_planilla' => 0,            // Se actualizará más tarde
                    ])->id;


                    // Insertar en la tabla detalle_planillas
                    foreach ($personalDetails as $detail) {

                        // Obtener el objeto de personal
                        $personal = Personal::where('dni', $detail['dni'])->first();


                        // Obtener IDs relacionados
                        $idPersonal = $personal->id;
                        $idCargo = $personal->cargo ? $personal->cargo->id : null;
                        $idAfp = $personal->afp ? $personal->afp->id : null;
                        $idOnp = $personal->onp ? $personal->onp->id : null;

                        

                        // Obtener los porcentajes de AFP y ONP
                        $afpPorcentajes = $idAfp ? DB::table('afp')->where('id', $idAfp)->first() : null;
                        $onpPorcentajes = $idOnp ? DB::table('onp')->where('id', $idOnp)->first() : null;

                        // Calcular la remuneración bruta
                         $remuneracion_bruta = $detail['remuneracion_mensual'] +  ($detail['remuneracion_vacacional'] ?? 0) ;
                         $rs_total=$detail['remuneracion_mensual'] +  ($detail['remuneracion_vacacional'] ?? 0)+ ($detail['cts'] ?? 0)+ ($detail['aguinaldo'] ?? 0) ;

                        // Calcular descuentos de AFP y ONP
                        $afp_descuento = 0;
                        $onp_descuento = 0;
                        if ($afpPorcentajes) {
                            $afp_fondo = ($remuneracion_bruta*$afpPorcentajes->comision_fija)/100 ?? 0;
                            $afp_ps = ($remuneracion_bruta*$afpPorcentajes->prima)/100 ?? 0;
                            $afp_c = ($remuneracion_bruta*$afpPorcentajes->comision_porcentual)/100 ?? 0;
                            $afp_descuento = ($afp_fondo + $afp_c + $afp_ps);
                        }
                        if ($onpPorcentajes) {
                            $onp_descuento = ($remuneracion_bruta * $onpPorcentajes->fondo / 100);
                        }

                         // Calcular aportes ESSALUD y SCTR
                        $essalud = $remuneracion_bruta * 0.09; // 9% de remuneración bruta
                        $sctr = $remuneracion_bruta * 0.0153; // 1.53% de remuneración bruta

                         // Calcular total detalle
                         $total_detalle = $rs_total+$essalud+$sctr;


                        // Insertar detalle en la tabla detalle_planillas

                        //calculo de los detalles de la planilla

                        DetallePlanilla::create([
                            'planilla_id' => $planillaId,
                            'personal_id' => $idPersonal,
                            'idcargo' => $idCargo,
                            'idafp' => $idAfp,
                            'idonp' => $idOnp,
                            'dni' => $detail['dni'],
                            'fecha_ingreso' => $fecha,
                            'dias_trabajados' => $detail['dias_trabajados'],
                            'remuneracion_mensual' => $detail['remuneracion_mensual'],
                            'aguinaldo' => $detail['aguinaldo'] ?? 0,
                            'remuneracion_vacacional' => $detail['remuneracion_vacacional'] ?? 0,
                            'cts' => $detail['cts'] ?? 0,
                            'descuentos_afp_fondo' => $afp_fondo ?? 0,
                            'descuentos_afp_c' => $afp_c ?? 0,
                            'descuentos_afp_ps' => $afp_ps ?? 0,
                            'aportes_essalud' => $essalud ?? 0,
                            'aportes_sctr' => $sctr ?? 0,
                            'total_detalle' => $total_detalle ?? 0,

                        ]);


                         // Actualizar los totales de la planilla
                            $totalRemuneracionBruta += $rs_total;
                            $totalDescuentosAfp += $afp_descuento;
                            $totalDescuentosOnp += $onp_descuento;
                            $totalAportesEssalud +=  $essalud;
                            $totalAportesSctr += $sctr;

                    }

                    // Actualizar la tabla planillas con los totales calculados
                        Planilla::where('id', $planillaId)->update([
                            'remuneracion_bruta_total' => $totalRemuneracionBruta,
                            'descuentos_afp' => $totalDescuentosAfp,
                            'descuentos_onp' => $totalDescuentosOnp,
                            'aportes_essalud' => $totalAportesEssalud,
                            'aportes_sctr' => $totalAportesSctr,
                            'total_planilla' => $totalRemuneracionBruta  + $totalAportesEssalud + $totalAportesSctr,
                        ]);


                });

                // Obtener todos los datos de la planilla y detalles después de la transacción
               //     $planilla = Planilla::findOrFail($planillaId);
                   $detalles = DetallePlanilla::where('planilla_id', $planillaId)->get();
                   $planilla = Planilla::findOrFail($planillaId);

                    // Responder con los datos de la planilla y detalles
                    return response()->json([                       
                       'planilla' => $planilla,
                        'detalles' => $detalles,
                   ], 201);

                // Responder con un mensaje de éxito
                //return response()->json(['message' => 'Datos guardados correctamente'], 201);
            }

            public function excel()
            {
                $planilla = Planilla::all()->toArray();       
                return array_reverse($planilla); 
            }


            
            public function destroy(Request $request)
            {
            
                $planilla = Planilla::destroy($request->id);       
                return $planilla;
                        
                
            }





   }
