<?php

namespace App\Http\Controllers;

use App\Models\Planilla;
use App\Models\DetallePlanilla;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
//use Illuminate\Support\Facades\Log; // Importa Log

class PlanillaController extends Controller
{
    public function index($id,$anio)
    {
        // Define el número de elementos por página
        $perPage = 20;

        $mes=$id;

        
        // Establece un tiempo de vida para la caché (en minutos)
        $cacheDuration = 60*480; // 60 minutos

        // Genera una clave de caché única basada en el periodo y la paginación
        $cacheKey = "planillas_periodo_{$mes}_{$anio}_page_" . request()->get('page', 1);

        // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
        $planillas = Cache::remember($cacheKey, $cacheDuration, function () use ($mes, $anio,$perPage) {
            // Construye la consulta base con eager loading para las relaciones
            $query = Planilla::with(['detallePlanillas.personal.cargo', 'detallePlanillas.personal.afp'])  
                            ->where('periodo', $mes)  // Filtra por el campo 'periodo' usando el valor de $mes
                            ->where('anio', $anio)  // Filtra por el campo 'periodo' usando el valor de $mes
                            ->orderBy('id', 'desc');
            
            // Obtén los elementos paginados
            return $query->paginate($perPage);
        });

        // Verifica si la clave está en caché
        //if (Cache::has($cacheKey)) {
            // Si la clave existe, significa que los datos están almacenados en caché
        //    Log::info("Los datos están almacenados en caché con la clave: {$cacheKey}");
        //} else {
        //    Log::info("Los datos NO están en caché, se ejecutó la consulta.");
       // }

    /////////////////////////////////////////////////////////////////




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
                        'flag' => $detalle->flag,

                        'personal' => [
                            'id' => $detalle->personal->id,
                            'nombres' => $detalle->personal->nombres,
                            'apellidos' => $detalle->personal->apellidos,
                            'dni' => $detalle->personal->dni,
                            'spp' => $detalle->personal->spp,
                            // Otros campos del modelo 'Personal' que necesites...
                        ],
                        'cargo' => [
                            'id' => $detalle->cargo->id,
                            'nombre_cargo' => $detalle->cargo->nombre_cargo,
                            
                            // Otros campos del modelo 'Personal' que necesites...
                        ],

                        'afp' => [
                            'id' => $detalle->afp->id,
                            'nombre_afp' => $detalle->afp->nombre_afp,
                            
                            // Otros campos del modelo 'Personal' que necesites...
                        ],
                        
                    ];
                })
            ];
        });

        // Retornar la variable $formattedData como JSON

        return response()->json($planillas);
   
    }




                public function show($id)
                {
                    $planilla = Planilla::with(['detallePlanillas.personal','detallePlanillas.personal.afp'])->find($id);


                    // Asumiendo que $id es el ID de la planilla que quieres buscar.
$cacheKey = "planilla_{$id}";

// Intenta recuperar la planilla desde el caché
$planilla = Cache::remember($cacheKey, 60*480, function () use ($id) {
    return Planilla::with(['detallePlanillas.personal', 'detallePlanillas.personal.afp'])->find($id);
});

                    

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


                        'id_usuario'=> $planilla->id_usuario,
                        'observaciones'=> $planilla->observaciones,



                        'remuneracion_bruta_total' => $planilla->remuneracion_bruta_total,
                        'descuentos_afp' => $planilla->descuentos_afp,
                        'descuentos_onp' => $planilla->descuentos_onp,

                        'descuentos_r4' => $planilla->descuentos_r4,
                        'descuentos_r5' => $planilla->descuentos_r5,

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

                                'ds311' => $detalle->ds311,
                                'ds313' => $detalle->ds313,

                                'aguinaldo' => $detalle->aguinaldo,
                                'remuneracion_vacacional' => $detalle->remuneracion_vacacional,
                                'cts' => $detalle->cts,
                                'descuentos_afp_fondo' => $detalle->descuentos_afp_fondo,
                                'descuentos_afp_sfp_c' => $detalle->descuentos_afp_sfp_c,
                                'descuentos_afp_ps' => $detalle->descuentos_afp_ps,
                                'descuentos_onp' => $detalle->descuentos_onp,
                                
                                'descuentos_r4' => $detalle->descuentos_r4,
                                'descuentos_r5' => $detalle->descuentos_r5,

                                'aportes_essalud' => $detalle->aportes_essalud,
                                'aportes_sctr' => $detalle->aportes_sctr,
                                'total_detalle' => $detalle->total_detalle,
                                'flag'=> $detalle->flag,
                                'personal' => [
                                    'id' => $detalle->personal->id,
                                    'nombres' => $detalle->personal->nombres,
                                    'apellidos' => $detalle->personal->apellidos,
                                    'dni' => $detalle->personal->dni,
                                    'spp' => $detalle->personal->spp,
                                    // Otros campos del modelo 'Personal' que necesites...
                                ],
                                'afp' => [
                                    'id' => $detalle->afp->id,
                                    'nombre_afp' => $detalle->afp->nombre_afp,
                                    
                                    // Otros campos del modelo 'Personal' que necesites...
                                ],
                            ];
                        })
                    ];

                    return response()->json($formattedData);
                }


               




            public function store(Request $request)
            {

              try {

                //dd($request->input('selectedYear'));

               
                // Validar los datos de entrada
                $validatedData = $request->validate([
                    'selectedPeriodo' => 'required|integer',
                    'selectedYear' => 'required|integer',
                    'selectedTipo' => 'required|integer',
                    'meta' => 'required|integer',
                    'UIT' => 'required|integer',

                    'id_usuario'=> 'required|integer',
                    'observaciones' => 'required|json', // Verificar que sea un JSON válido

                    'fechaActual' => 'required|date',
                    'nuevoArray' => 'required|array',
                    'nuevoArray.*.dni' => 'required|string',
                    'nuevoArray.*.fecha_ingreso' => 'required|date',
                    'nuevoArray.*.dias_trabajados' => 'required|integer',
                    'nuevoArray.*.remuneracion_mensual' => 'required|numeric',

                    'nuevoArray.*.ds311' => 'required|numeric',
                    'nuevoArray.*.ds313' => 'required|numeric',

                    'nuevoArray.*.aguinaldo' => 'nullable|numeric',
                    'nuevoArray.*.remuneracion_vacacional' => 'nullable|numeric',
                    'nuevoArray.*.cts' => 'nullable|numeric',
                    'nuevoArray.*.r4' => 'nullable|boolean',
                    'nuevoArray.*.r5' => 'nullable|boolean',
                    'nuevoArray.*.r5Input' => 'nullable|numeric',
                    'nuevoArray.*.flag' => 'nullable|numeric',
                   
                ]);

                // Obtener los datos del formulario
                $anio=$validatedData['selectedYear'];
                $periodo = $validatedData['selectedPeriodo'];
                $tipo_planilla = $validatedData['selectedTipo'];
                $numero_planilla = $validatedData['meta'];

                $UIT = $validatedData['UIT'];

                $id_usuario=$validatedData['id_usuario'];
                $observaciones=$validatedData['observaciones'];

                $fecha = $validatedData['fechaActual'];

                $personalDetails = $validatedData['nuevoArray'];


                    // Inicializar el planillaId como null
                $planillaId = null;

                // Iniciar la transacción
                DB::transaction(function () use ($numero_planilla, $tipo_planilla,$fecha,$id_usuario,$observaciones,$periodo,$anio,$UIT,$personalDetails, &$planillaId) {
                    // Insertar en la tabla planillas
                    $totalRemuneracionBruta =0;

                    $totalds311=0;
                    $totalds313=0;

                    $totalDescuentosAfp = 0;
                    $totalDescuentosOnp = 0;
                    $totalDescuentos_r4 = 0;
                    $totalDescuentos_r5 = 0;
                    $totalAportesEssalud = 0;
                    $totalAportesSctr = 0;
                    // Insertar en la tabla planillas (sin valores iniciales)
                    $planillaId = Planilla::create([
                        'anio'=> $anio,
                        'periodo'=> $periodo,
                        'numero_planilla' => $numero_planilla,
                        'tipo_planilla' => $tipo_planilla,
                        'fecha' => $fecha,
                         
                        'id_usuario'=> $id_usuario,
                        'observaciones'=> $observaciones,


                        'remuneracion_bruta_total' => 0,  // Se actualizará más tarde

                        'ds311' => 0,  // Se actualizará más tarde
                        'ds313' => 0,  // Se actualizará más tarde

                        'descuentos_afp' => 0,            // Se actualizará más tarde
                        'descuentos_onp' => 0,            // Se actualizará más tarde
                        'descuentos_r4' => 0,            // Se actualizará más tarde
                        'descuentos_r5' => 0,            // Se actualizará más tarde
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

                         
                        $t_ds311= $detail['ds311'];
                        $t_ds313= $detail['ds313'];


                         $remuneracion_bruta = $detail['remuneracion_mensual'] +  ($detail['remuneracion_vacacional'] ?? 0) ;
                         $rs_total=$detail['remuneracion_mensual'] +$detail['ds311']+$detail['ds313'] + ($detail['remuneracion_vacacional'] ?? 0)+ ($detail['cts'] ?? 0)+ ($detail['aguinaldo'] ?? 0) ;

                        // Calcular descuentos de AFP y ONP preguntar sieltipo de planilla es CAS

                        $afp_descuento = 0;
                        $onp_descuento = 0;

                        if ($afpPorcentajes) {
                                $afp_fondo = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->comision_fija) / 100,2) ?? 0;
                                $afp_ps = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->prima) / 100,2) ?? 0;
                                $afp_c = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->comision_porcentual) / 100,2) ?? 0;
                                $afp_descuento = round(($afp_fondo+ $afp_c + $afp_ps),2);
                        }
                        if ($onpPorcentajes) {
                            $onp_descuento = round((($remuneracion_bruta+$t_ds311+$t_ds313 )* $onpPorcentajes->fondo / 100),2);
                        }


                        $essalud=0;
                        $sctr = 0;



                        $uit= $UIT; // Valor de la UIT, se obtiene del request
                        




                        $puit_cas=round($uit*0.45,2); //2407.50

                        $d_r4=0;

                        $d_r5= $detail['r5Input'];

                        $flag= $detail['flag'];



                        if ($tipo_planilla==3){
                            //PLANILLA CAS
                        

                            if ($remuneracion_bruta >= $puit_cas)
                            {
                                $essalud = round($puit_cas * 0.09,2); // 9% de remuneración bruta  
                                //$onp_descuento = round((($remuneracion_bruta )* $onpPorcentajes->fondo / 100),2);
                            }
                            else{
                                //$essalud = round($remuneracion_bruta * 0.09,2); // 9% de remuneración bruta
                                $essalud = round(($remuneracion_bruta +$t_ds311+$t_ds313)* 0.09,2); // 9% de remuneración bruta
                            }

                          if ($detail['r4']){
                                //$d_r4=round(($remuneracion_bruta +$detail['aguinaldo'])*0.08,2);
                                $d_r4=round(($remuneracion_bruta+$t_ds311+$t_ds313 +$detail['aguinaldo'])*0.08,2);


                            }

                            

                        }
                        else{ 
                            if ($tipo_planilla==7){
                                //PLANILLA RACIONAMIENTO

                                $essalud=0;
                                $sctr = 0;

                            }elseif($tipo_planilla==8){
                                //PLANILLA VACACIONES
                                //$essalud=0;
                                $essalud = round(($remuneracion_bruta + $detail['ds311']+$detail['ds313'])* 0.09,2); // 9% de remuneración bruta  
                                $sctr = 0;
                            }elseif($tipo_planilla==9){
                                //PLANILLA CTS
                                $essalud=0;
                                $sctr = 0;
                            }
                            else{  
                            
                                    // Calcular aportes ESSALUD y SCTR
                                    if (($remuneracion_bruta+$t_ds311+$t_ds313) >= 1130)
                                    {
                                        $essalud = round(($remuneracion_bruta + $detail['ds311']+$detail['ds313'])* 0.09,2); // 9% de remuneración bruta  
                                    }
                                    else{
                                        
                                        if($flag==1){
                                            $essalud = round(($remuneracion_bruta+$detail['ds311']+$detail['ds313']) * 0.09,2); // 9% de remuneración bruta

                                        }else{
                                            $essalud = round((1130+$detail['ds311']+$detail['ds313']) * 0.09,2); // 9% de remuneración bruta
                                        }
                                    }
                                    //$essalud = $remuneracion_bruta * 0.09; // 9% de remuneración bruta
                                    if ($tipo_planilla<5)
                                    {
                                        $sctr = round(($remuneracion_bruta+ $detail['ds311'] + $detail['ds313']) * 0.0153,2); // 1.53% de remuneración bruta
                                    }
                                    else{
                                        $sctr = 0;
                                    }

                                    // Calcular total detalle

                                }  


                        }//


                            $total_detalle = round($rs_total+$t_ds311+$t_ds313 + $essalud + $sctr,2);


                        // Insertar detalle en la tabla detalle_planillas

                        //calculo de los detalles de la planilla


                        //dd($d_r4);

                        DetallePlanilla::create([
                            'planilla_id' => $planillaId,
                            'personal_id' => $idPersonal,
                            'idcargo' => $idCargo,
                            'idafp' => $idAfp,
                            'idonp' => $idOnp,
                            'dni' => $detail['dni'],
                            'fecha_ingreso' => $detail['fecha_ingreso'],
                            'dias_trabajados' => $detail['dias_trabajados'],
                            'remuneracion_mensual' => $detail['remuneracion_mensual'],

                            'ds311' => $detail['ds311'],
                            'ds313' => $detail['ds313'],

                            'aguinaldo' => $detail['aguinaldo'] ?? 0,
                            'remuneracion_vacacional' => $detail['remuneracion_vacacional'] ?? 0,
                            'cts' => $detail['cts'] ?? 0,
                            'descuentos_afp_fondo' => $afp_fondo ?? 0,
                            'descuentos_afp_c' => $afp_c ?? 0,
                            'descuentos_afp_ps' => $afp_ps ?? 0,
                            'descuentos_onp' => $onp_descuento ?? 0,
                            'descuentos_r4' => $d_r4 ?? 0,
                            'descuentos_r5' => $d_r5 ?? 0,
                            'aportes_essalud' => $essalud ?? 0,
                            'aportes_sctr' => $sctr ?? 0,
                            'total_detalle' => $total_detalle ?? 0,

                            'flag' => $detail['flag'] ?? 0,

                        ]);


                         // Actualizar los totales de la planilla
                            $totalRemuneracionBruta += $rs_total;
                            $totalDescuentosAfp += $afp_descuento;
                            $totalDescuentosOnp += $onp_descuento;

                            $totalds311 +=  $t_ds311;
                            $totalds313 +=  $t_ds313;

                            $totalDescuentos_r4 += $d_r4;
                            $totalDescuentos_r5 += $d_r5;

                            $totalAportesEssalud +=  $essalud;
                            $totalAportesSctr += $sctr;

                    }

                    // Actualizar la tabla planillas con los totales calculados
                        Planilla::where('id', $planillaId)->update([
                            'remuneracion_bruta_total' => $totalRemuneracionBruta,

                            'ds311' => $t_ds311,
                            'ds313' => $t_ds313,

                            'descuentos_afp' => $totalDescuentosAfp,
                            'descuentos_onp' => $totalDescuentosOnp,
                            'descuentos_r4' => $totalDescuentos_r4,
                            'descuentos_r5' => $totalDescuentos_r5,
                            'aportes_essalud' => $totalAportesEssalud,
                            'aportes_sctr' => $totalAportesSctr,
                            
                            'total_planilla' => $totalRemuneracionBruta +$totalds311+$totalds313+ $totalAportesEssalud + $totalAportesSctr,


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
             
            
        } catch (\Exception $e) {
            // Captura y muestra el error
            return response()->json(['error' => $e->getMessage()], 500);
        }

            }

            
            public function modifica(Request $request, $id)
            {
                
                    // Validar los datos de entrada


               try {     
                        $validatedData = $request->validate([

                        'selectedYear' => 'required|integer',
                        'UIT' => 'required|integer',

                        'selectedPeriodo' => 'required|integer',
                        'selectedTipo' => 'required|integer',
                        'meta' => 'required|integer',
                        'fechaActual' => 'required|date',

                        'id_usuario'=> 'required|integer',
                        'observaciones' => 'required|json', // Verificar que sea un JSON válido


                        'nuevoArray' => 'required|array',
                        'nuevoArray.*.dni' => 'required|string',
                        'nuevoArray.*.fecha_ingreso' => 'required|date',
                        'nuevoArray.*.dias_trabajados' => 'required|integer',
                        'nuevoArray.*.remuneracion_mensual' => 'required|numeric',

                        'nuevoArray.*.ds311' => 'required|numeric',
                        'nuevoArray.*.ds313' => 'required|numeric',



                        'nuevoArray.*.aguinaldo' => 'nullable|numeric',
                        'nuevoArray.*.remuneracion_vacacional' => 'nullable|numeric',
                        'nuevoArray.*.cts' => 'nullable|numeric',
                        'nuevoArray.*.r4' => 'nullable|boolean',
                        'nuevoArray.*.r5' => 'nullable|boolean',
                        'nuevoArray.*.r5Input' => 'nullable|numeric',

                        'nuevoArray.*.flag' => 'nullable|numeric',


                    ]);
        
                        // Obtener los datos del formulario
                        $periodo = $validatedData['selectedPeriodo'];
                        $anio = $validatedData['selectedYear'];
                        $tipo_planilla = $validatedData['selectedTipo'];
                        $numero_planilla = $validatedData['meta'];

                        $UIT = $validatedData['UIT'];

                        $fecha = $validatedData['fechaActual'];

                        $id_usuario=$validatedData['id_usuario'];
                        $observaciones=$validatedData['observaciones'];

                        $personalDetails = $validatedData['nuevoArray'];

                       // dd($personalDetails);
        
                        // Iniciar la transacción
                        DB::transaction(function () use ($id, $numero_planilla, $tipo_planilla, $anio,$UIT,$periodo, $fecha,$id_usuario,$observaciones, $personalDetails) {
                            // Actualizarlatablaplanillas
                            $totalRemuneracionBruta = 0;

                            $totalds311=0;
                            $totalds313=0;

                            $totalDescuentosAfp = 0;
                            $totalDescuentosOnp = 0;
                            $totalDescuentos_r4 = 0;
                            $totalDescuentos_r5 = 0;
                            $totalAportesEssalud = 0;
                            $totalAportesSctr = 0;
        
                        // Actualizar la planilla existente
                        Planilla::where('id', $id)->update([
                            'anio' => $anio,
                            'periodo' => $periodo,
                            'numero_planilla' => $numero_planilla,
                            'tipo_planilla' => $tipo_planilla,
                            'fecha' => $fecha,

                            'id_usuario'=> $id_usuario,
                            'observaciones'=> $observaciones,
                             

                            'remuneracion_bruta_total' => 0,  // Se actualizará más tarde

                            'ds311' => 0,  // Se actualizará más tarde
                            'ds313' => 0,  // Se actualizará más tarde
                                                    
                            'descuentos_afp' => 0,            // Se actualizará más tarde
                            'descuentos_onp' => 0,            // Se actualizará más tarde
                            'descuentos_r4' => 0,            // Se actualizará más tarde
                            'descuentos_r5' => 0,            // Se actualizará más tarde
                            'aportes_essalud' => 0,           // Se actualizará más tarde
                            'aportes_sctr' => 0,              // Se actualizará más tarde
                            'total_planilla' => 0,            // Se actualizará más tarde
                        ]);
        
                        // Eliminar los detalles existentes para este planillaDetalle
                        DetallePlanilla::where('planilla_id', $id)->delete();

        
                        // Insertar los nuevos detalles en la tabla detalle_planillas
                        foreach ($personalDetails as $detail) {
                        // Obtener el objeto de personal
                            $personal = Personal::where('dni', $detail['dni'])->first();
        
                            // Obtener IDs relacionados
                            $idPersonal = $personal->id;
                            $idCargo = $personal->cargo ? $personal->cargo->id : null;
                            $idAfp = $personal->afp ? $personal->afp->id : null;
                            $idOnp = $personal->onp ? $personal->onp->id : null;

                           // $fecha_ingreso_p=$personal->fecha_ingreso;
        
                            // Obtener los porcentajes de AFP y ONP
                            $afpPorcentajes = $idAfp ? DB::table('afp')->where('id', $idAfp)->first() : null;
                            $onpPorcentajes = $idOnp ? DB::table('onp')->where('id', $idOnp)->first() : null;


                            $t_ds311= $detail['ds311'];
                            $t_ds313= $detail['ds313'];
        
                            // Calcular la remuneración bruta
                            $remuneracion_bruta = $detail['remuneracion_mensual'] +  ($detail['remuneracion_vacacional'] ?? 0);
                            $rs_total = $detail['remuneracion_mensual'] +  ($detail['remuneracion_vacacional'] ?? 0) + ($detail['cts'] ?? 0) + ($detail['aguinaldo'] ?? 0);
        
        
                            // Calcular descuentos de AFP y ONP
                            $afp_descuento = 0;
                            $onp_descuento = 0;
                            if ($afpPorcentajes) {
                                $afp_fondo = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->comision_fija) / 100,2) ?? 0;
                                $afp_ps = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->prima) / 100,2) ?? 0;
                                $afp_c = round((($remuneracion_bruta+$t_ds311+$t_ds313) * $afpPorcentajes->comision_porcentual) / 100,2) ?? 0;
                                $afp_descuento = round(($afp_fondo+ $afp_c + $afp_ps),2);
                            }
                            if ($onpPorcentajes) {
                                $onp_descuento = round((($remuneracion_bruta+$t_ds311+$t_ds313 )* $onpPorcentajes->fondo / 100),2);
                            }
        
                            // Calcular aportes ESSALUD y SCTR
                            $essalud=0;

                            //$essalud=0;
                            $sctr = 0;

                            $uit= $UIT; // Valor de la UIT, se obtiene del request
                            //$uit= 5350;


                            $puit_cas=round($uit*0.45,2);

                            $d_r4=0;
                            
                            $d_r5= $detail['r5Input'];

                            
                           $flag= $detail['flag'];
    

    
                            if ($tipo_planilla==3){
                               
    
                                if ($remuneracion_bruta >= $puit_cas)
                                {
                                    $essalud = round(($puit_cas) * 0.09,2); // 9% de remuneración bruta  
                                }
                                else{
                                    $essalud = round(($remuneracion_bruta +$t_ds311+$t_ds313)* 0.09,2); // 9% de remuneración bruta
                                }

                                if ($detail['r4']){
                                    $d_r4=round(($remuneracion_bruta+$t_ds311+$t_ds313 +$detail['aguinaldo'])*0.08,2);
                                }
                                

                         
                            }elseif($tipo_planilla==8){
                                //$essalud=0;
                                $essalud = round(($remuneracion_bruta + $detail['ds311']+$detail['ds313'])* 0.09,2); // 9% de remuneración bruta  

                                $sctr = 0;
                            }elseif($tipo_planilla==9){
                                $essalud=0;
                                $sctr = 0;
                            }
                            else{

                                if ($tipo_planilla==7){

                                    $essalud=0;
                                    $sctr = 0;
    
    
    
                                }
                                else{ 


                                    
                                    
                                                               
                                       // Calcular aportes ESSALUD y SCTR
                                    if (($remuneracion_bruta+$t_ds311+$t_ds313) >= 1130)
                                    {
                                        $essalud = round(($remuneracion_bruta + $detail['ds311']+$detail['ds313'])* 0.09,2); // 9% de remuneración bruta  
                                    }
                                    else{

                                        if($flag==1){
                                            $essalud = round(($remuneracion_bruta+$detail['ds311']+$detail['ds313']) * 0.09,2); // 9% de remuneración bruta
                                        }else{
                                            $essalud = round((1130+$detail['ds311']+$detail['ds313']) * 0.09,2); // 9% de remuneración bruta
                                        }    

                                    }
                                    //$essalud = $remuneracion_bruta * 0.09; // 9% de remuneración bruta
                                    if ($tipo_planilla<5)
                                    {
                                        $sctr = round(($remuneracion_bruta + $detail['ds311'] + $detail['ds313'])* 0.0153,2); // 1.53% de remuneración bruta
                                    }
                                    else{
                                        $sctr = 0;
                                    }


                                    //$essalud = $remuneracion_bruta * 0.09; // 9% de remuneración bruta
                                    //if ($tipo_planilla!=5){
                                    //  $sctr = round($remuneracion_bruta * 0.0153,2);  // 1.53% de remuneración bruta
                                    //}



                                } 
                             }  //ELSE     
                            // Calcular total detalle

                           // dd($t_ds311);


                            $total_detalle = round($rs_total+$t_ds311+$t_ds313 + $essalud + $sctr,2);

                            //dd($total_detalle);
        
                            // Insertar detalle en la tabla detalle_planillas
                            DetallePlanilla::create([
                                'planilla_id' => $id,
                                'personal_id' => $idPersonal,
                                'idcargo' => $idCargo,
                                'idafp' => $idAfp,
                                'idonp' => $idOnp,
                                'dni' => $detail['dni'],

                                'fecha_ingreso' => $detail['fecha_ingreso'],

                                'dias_trabajados' => $detail['dias_trabajados'],
                                'remuneracion_mensual' => $detail['remuneracion_mensual'],
                                

                                'ds311' => $detail['ds311'],
                                'ds313' => $detail['ds313'], 


                                'aguinaldo' => $detail['aguinaldo'] ?? 0,
                                'remuneracion_vacacional' => $detail['remuneracion_vacacional'] ?? 0,
                                'cts' => $detail['cts'] ?? 0,
                                'descuentos_afp_fondo' => $afp_fondo ?? 0,
                                'descuentos_afp_c' => $afp_c ?? 0,
                                'descuentos_afp_ps' => $afp_ps ?? 0,
                                'descuentos_onp' => $onp_descuento ?? 0,

                                'descuentos_r4' => $d_r4 ?? 0,
                                'descuentos_r5' => $d_r5 ?? 0, 

                                'aportes_essalud' => $essalud ?? 0,
                                'aportes_sctr' => $sctr ?? 0,
                                'total_detalle' => $total_detalle ?? 0,

                                'flag' => $detail['flag'] ?? 0,


                            ]);
        
                            // Actualizar los totales de la planilla
                            $totalRemuneracionBruta += $rs_total;

                            $totalds311 +=  $t_ds311;
                            $totalds313 +=  $t_ds313;

                            $totalDescuentosAfp += $afp_descuento;
                            $totalDescuentosOnp += $onp_descuento;

                            $totalDescuentos_r4 += $d_r4;
                            $totalDescuentos_r5 += $d_r5;


                            $totalAportesEssalud += $essalud;
                            $totalAportesSctr += $sctr;
                        }
        
                        // Actualizar la tabla planillas con los totales calculados
                        Planilla::where('id', $id)->update([
                            'remuneracion_bruta_total' => $totalRemuneracionBruta,

                            'ds311' => $t_ds311,
                            'ds313' => $t_ds313,


                            'descuentos_afp' => $totalDescuentosAfp,
                            'descuentos_onp' => $totalDescuentosOnp,

                            'descuentos_r4' => $totalDescuentos_r4,
                            'descuentos_r5' => $totalDescuentos_r5,

                            'aportes_essalud' => $totalAportesEssalud,
                            'aportes_sctr' => $totalAportesSctr,
                            'total_planilla' => $totalRemuneracionBruta +$totalds311+$totalds313+ $totalAportesEssalud + $totalAportesSctr,
                        ]);
                });
                return response()->json(['message' => 'Planilla y detalles actualizados correctamente']);

            } catch (\Exception $e) {
                // Captura y muestra el error
                return response()->json(['error' => $e->getMessage()], 500);
            }

        }



            public function excel()
            {
                $planilla = Planilla::all()->toArray();       
                return array_reverse($planilla); 
            }

            public function periodoexcel($id)
            {
                        $planillas = Planilla::where('periodo', $id)
                        ->where('tipo_planilla', '!=', 7) // Excluir las planillas donde tipo_planilla es 7 racionamiento
                         ->orderBy('id', 'desc')
                         ->get()
                         ->toArray();

                        // Invierte el orden de las planillas
                        return array_reverse($planillas); 
            }

            public function periodoexcelraci($id)
            {
                        $planillas = Planilla::where('periodo', $id)
                        ->where('tipo_planilla', '=', 7) // incluir las planillas donde tipo_planilla es 7 racionamiento
                         ->orderBy('id', 'desc')
                         ->get()
                         ->toArray();

                        // Invierte el orden de las planillas
                        return array_reverse($planillas); 
            }


            public function periodoexceltotal($id)
            {
                        $planillas = Planilla::where('periodo', $id)                       
                         ->orderBy('id', 'desc')
                         ->get()
                         ->toArray();

                        // Invierte el orden de las planillas
                        return array_reverse($planillas); 
            }







            public function validarperiodo($id)
{
    // Obtener las planillas filtradas por el periodo
    $planillas = Planilla::where('periodo', $id)->with('detallePlanillas')->get();

    // Array para almacenar las diferencias
    $diferencias = [];

    foreach ($planillas as $planilla) {
        // Sumar los valores de los campos en la tabla DetallePlanilla
        $remuneracionBrutaTotal = $planilla->detallePlanillas->sum(function ($detalle) {
            return $detalle->remuneracion_mensual + $detalle->aguinaldo + $detalle->remuneracion_vacacional + $detalle->cts;
        });
        $descuentosAfpTotal = round(
            $planilla->detallePlanillas->sum('descuentos_afp_fondo') +
            $planilla->detallePlanillas->sum('descuentos_afp_c') +
            $planilla->detallePlanillas->sum('descuentos_afp_ps'), 2
        );
        
        $descuentosOnpTotal = round($planilla->detallePlanillas->sum('descuentos_onp'), 2);
        $descuentosR4Total = round($planilla->detallePlanillas->sum('descuentos_r4'), 2);
        $descuentosR5Total = round($planilla->detallePlanillas->sum('descuentos_r5'), 2);
        $aportesEssaludTotal = round($planilla->detallePlanillas->sum('aportes_essalud'), 2);
        $aportesSctrTotal = round($planilla->detallePlanillas->sum('aportes_sctr'), 2);

        // Array para almacenar los campos con errores
        $errores = [];

        // Comparar las sumas con los valores en la tabla Planilla y agregar al array de errores si hay diferencias
        if ($remuneracionBrutaTotal != $planilla->remuneracion_bruta_total) {
            $errores[] = 'remuneracion_bruta_total  '.$remuneracionBrutaTotal.'   '.$planilla->remuneracion_bruta_total;
        }
        if ($descuentosAfpTotal != $planilla->descuentos_afp) {
            $errores[] = 'descuentos_afp '.$descuentosAfpTotal." ".$planilla->descuentos_afp;
        }
        if ($descuentosOnpTotal != $planilla->descuentos_onp) {
            $errores[] = 'descuentos_onp '.$descuentosOnpTotal." ".$planilla->descuentos_onp;
        }
        if ($descuentosR4Total != $planilla->descuentos_r4) {
            $errores[] = 'descuentos_r4 '.$descuentosR4Total."  ".$planilla->descuentos_r4;
        }
        if ($descuentosR5Total != $planilla->descuentos_r5) {
            $errores[] = 'descuentos_r5  '."  ".$descuentosR5Total."  ".$planilla->descuentos_r5;
        }
        if ($aportesEssaludTotal != $planilla->aportes_essalud) {
            $errores[] = 'aportes_essalud  '." ".$aportesEssaludTotal."   ".$planilla->aportes_essalud ;
        }
        if ($aportesSctrTotal != $planilla->aportes_sctr) {
            $errores[] = 'aportes_sctr  '." ".$aportesSctrTotal." ".$planilla->aportes_sctr;
        }

        // Si hay errores, agregamos la planilla y los errores al array de diferencias
        if (!empty($errores)) {
            $diferencias[] = [
                'planilla_id' => $planilla->id,
                'numero_planilla' => $planilla->numero_planilla,
                'errores' => $errores,
                'remuneracion_bruta_total' => $planilla->remuneracion_bruta_total,
                'remuneracion_bruta_detalle' => $remuneracionBrutaTotal,
                'descuentos_afp_total' => $planilla->descuentos_afp,
                'descuentos_afp_detalle' => $descuentosAfpTotal,
                'descuentos_onp_total' => $planilla->descuentos_onp,
                'descuentos_onp_detalle' => $descuentosOnpTotal,
                'descuentos_r4_total' => $planilla->descuentos_r4,
                'descuentos_r4_detalle' => $descuentosR4Total,
                'descuentos_r5_total' => $planilla->descuentos_r5,
                'descuentos_r5_detalle' => $descuentosR5Total,
                'aportes_essalud_total' => $planilla->aportes_essalud,
                'aportes_essalud_detalle' => $aportesEssaludTotal,
                'aportes_sctr_total' => $planilla->aportes_sctr,
                'aportes_sctr_detalle' => $aportesSctrTotal,
            ];
        }
    }

    // Devolver las diferencias en formato JSON
    if (!empty($diferencias)) {
        return response()->json($diferencias);
    } else {
        return response()->json(['message' => 'No se encontraron diferencias.'], 200);
    }
}

            

            public function dni_rm($id)
            {
                $ultimaRemuneracion = DetallePlanilla::where('dni', $id)
                ->latest('created_at') // Asegúrate de ordenar por el campo de fecha adecuado
                ->first();
            
            if ($ultimaRemuneracion) {
                return $ultimaRemuneracion->toArray(); // Convierte a array si no es null
            } else {
                return []; // Retorna un array vacío si no se encuentra ningún registro
            } 
            }


            public function detalle_excel($id,$anio)
            {               

                  // Obtener los detalles de planillas filtrados por el periodo en la tabla Planillas
                  //->where('tipo_planilla', '!=', 7) // Excluir las planillas donde tipo_planilla es 7 racionamiento
                    $planillas = DetallePlanilla::with(['personal', 'planilla'])
                    ->whereHas('planilla', function($query) use ($id,$anio) {
                        $query->where('periodo', $id)  // Filtrar por el campo 'periodo' en la tabla 'planillas'
                        ->where('anio', $anio)  // filtar el año
                        ->where('tipo_planilla', '!=', 7);  // Excluir los registros con tipo_planilla = 7
                    })
                    ->orderBy('dni', 'desc')  // Ordenar por 'dni' en la tabla 'detalle_planillas'
                    ->get();

                     // Modificar los datos para incluir el tipo de planilla
                    $planillasArray = $planillas->map(function($detalle) {
                        $detalleArray = $detalle->toArray();  // Convertir el modelo a array
                        $detalleArray['tipo_planilla'] = $detalle->planilla->tipo_planilla;  // Agregar el tipo de planilla desde la relación
                        return $detalleArray;
                    })->toArray();         

                // Retornar los datos como array
                return array_reverse( $planillasArray );
            }

            public function detalle_txt($id)
            {               

                  // Obtener los detalles de planillas filtrados por el periodo en la tabla Planillas
                    $planillas = DetallePlanilla::with(['personal', 'planilla'])
                    ->whereHas('planilla', function($query) use ($id) {
                        $query->where('id', $id);  // Filtrar por el campo 'periodo' en la tabla 'planillas'
                    })
                    ->orderBy('dni', 'desc')  // Ordenar por 'dni' en la tabla 'detalle_planillas'
                    ->get();

                     // Modificar los datos para incluir el tipo de planilla
                    $planillasArray = $planillas->map(function($detalle) {
                        $detalleArray = $detalle->toArray();  // Convertir el modelo a array
                        $detalleArray['tipo_planilla'] = $detalle->planilla->tipo_planilla;  // Agregar el tipo de planilla desde la relación
                        return $detalleArray;
                    })->toArray();         

                // Retornar los datos como array
                return array_reverse( $planillasArray );
            }


   
            public function obtener_ia(Request $request)
            {
                try {
                    // Valida los datos que vienen en el request, asumiendo que necesitas un parámetro 'question'
                    $request->validate([
                        'question' => 'required|string',
                    ]);

                    // Prepara la URL de la API
                    //$url = 'http://localhost:5000/query';
                    //urt
                    $url = 'https://ia.facturame.online/api/query';

                    // Configura los datos para enviar
                    $data = [
                        'question' => $request->input('question'),
                    ];

                    // Convierte los datos a JSON
                    $jsonData = json_encode($data);

                    // Configura las opciones de contexto para la solicitud HTTP
                    $options = [
                        'http' => [
                            'header' => [
                                "Content-Type: application/json",
                                "Content-Length: " . strlen($jsonData),
                            ],
                            'method' => 'POST',
                            'content' => $jsonData,
                        ],
                    ];

                    // Crea el contexto de la solicitud
                    $context = stream_context_create($options);

                    // Realiza la solicitud HTTP y obtén la respuesta
                    $response = file_get_contents($url, false, $context);

                    // Si la respuesta es false, maneja el error
                    if ($response === false) {
                        return response()->json(['error' => 'Error en la solicitud a la API'], 500);
                    }

                    // Decodifica la respuesta JSON
                    $data = json_decode($response, true);

                    // Retorna la respuesta como JSON
                    return response()->json($data);
                } catch (\Exception $e) {
                    // Maneja cualquier error
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }

            public function ia_datos(Request $request)
            {
                try {
                    // Valida los datos que vienen en el request, asumiendo que necesitas un parámetro 'question'
                    $request->validate([
                        'question' => 'required|string',
                    ]);

                    // Prepara la URL de la API
                    //$url = 'http://localhost:5001/query';
                    //urt
                    $url = 'https://calculos.facturame.online/api/query';

                    // Configura los datos para enviar
                    $data = [
                        'question' => $request->input('question'),
                    ];

                    // Convierte los datos a JSON
                    $jsonData = json_encode($data);

                    // Configura las opciones de contexto para la solicitud HTTP
                    $options = [
                        'http' => [
                            'header' => [
                                "Content-Type: application/json",
                                "Content-Length: " . strlen($jsonData),
                            ],
                            'method' => 'POST',
                            'content' => $jsonData,
                        ],
                    ];

                    // Crea el contexto de la solicitud
                    $context = stream_context_create($options);

                    // Realiza la solicitud HTTP y obtén la respuesta
                    $response = file_get_contents($url, false, $context);

                    // Si la respuesta es false, maneja el error
                    if ($response === false) {
                        return response()->json(['error' => 'Error en la solicitud a la API'], 500);
                    }

                    // Decodifica la respuesta JSON
                    $data = json_decode($response, true);

                    // Retorna la respuesta como JSON
                    return response()->json($data);
                } catch (\Exception $e) {
                    // Maneja cualquier error
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
 
           

            


            
            public function destroy(Request $request)
            {
            
                $planilla = Planilla::destroy($request->id);       
                return $planilla;
                        
                
            }






   }
