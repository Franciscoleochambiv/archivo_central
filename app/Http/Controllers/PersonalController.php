<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class PersonalController extends Controller
{
    public function index(Request $request)
{
    // Define el número de elementos por página
    $perPage = 10;

   /* Metodo 1 
    // Obtén el término de búsqueda desde la consulta
    $searchTerm = $request->input('q');

    // Construye la consulta base con eager loading para las relaciones
   // $query = Personal::with(['cargo', 'afp', 'onp'])
   //                  ->when(strlen($searchTerm) >= 3, function ($query) use ($searchTerm) {
   //                      $query->where('apellidos', 'LIKE', "%{$searchTerm}%");
   //                  })
   //                  ->orderBy('id', 'desc');
   $query = Personal::with(['cargo', 'afp', 'onp'])
    ->when(strlen($searchTerm) >= 3, function ($query) use ($searchTerm) {
        $query->where(function ($query) use ($searchTerm) {
            $query->where('nombres', 'LIKE', "%{$searchTerm}%")
                ->orWhere('apellidos', 'LIKE', "%{$searchTerm}%")
                ->orWhere('dni', 'LIKE', "%{$searchTerm}%")
                ->orWhere('spp', 'LIKE', "%{$searchTerm}%");
        });
    })
    ->orderBy('id', 'desc');

    // Obtén los elementos paginados
    $personals = $query->paginate($perPage);


    */

      // Obtén el término de búsqueda desde la consulta
      $searchTerm = $request->input('q');

      // Genera una clave de caché única basada en el término de búsqueda y la paginación
      $cacheKey = "personals_search_" . md5($searchTerm) . "_page_" . $request->get('page', 1);
  
      // Establece la duración del caché en minutos (por ejemplo, 60 minutos)
      $cacheDuration = 60*480; // Cambia este valor según lo necesites
  
      // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
      $personals = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
          // Construye la consulta base con las relaciones necesarias
          $query = Personal::with(['cargo', 'afp', 'onp'])
              ->when(strlen($searchTerm) >= 3, function ($query) use ($searchTerm) {
                  $query->where(function ($query) use ($searchTerm) {
                      $query->where('nombres', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('apellidos', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('dni', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('spp', 'LIKE', "%{$searchTerm}%");
                  });
              })
              ->orderBy('id', 'desc');
  
          // Obtén los elementos paginados
          return $query->paginate($perPage);
      });





    // Revertir el orden de los elementos
    $data = $personals->items();
    //$data = array_reverse($data);

    // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
    $personals = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,
        $personals->total(),
        $personals->perPage(),
        $personals->currentPage(),
        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    );

    return response()->json($personals);
}


    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {

                    $personal = new Personal;                
                    $personal->apellidos = $request->apellidos;
                    $personal->nombres = $request->nombres;
                    
                    $personal->email = $request->email;

                    $personal->dni = $request->dni;

                    //$personal->spp = $request->spp;
                    $personal->spp = $request->spp ?? '';
                    

                    $personal->fecha_nacimiento = $request->fecha_nacimiento;
                    $personal->idcargo = $request->cargos;
                    $personal->idafp = $request->afp;
                    $personal->idonp = $request->onp;
                    $personal->fecha_ingreso = $request->fecha_ingreso;

                    $personal->device_user_id = $request->device_user_id;
                    $personal->remuneracion_mensual = $request->remuneracion_mensual;


                    $personal->ip_address = $request->ip_address;        

                    $personal->obra_id = $request->obra_id;        

                    $personal->tipo_contrato = $request->tipo_contrato;
                    $personal->mac_address = $request->mac_address;
                    $personal->android_id = $request->android_id;

                    $personal->save();
                    return response()->json('The post successfully added');

        }catch (QueryException $e) {
        if ($e->errorInfo[1] == 1062) {
            // 1062 = Duplicado en MySQL
            return response()->json(['message' => 'El personal ya existe con ese DNI'], 422);
        }
        // Otro error
         return response()->json(['message' => 'Error inesperado al registrar el personal'], 500);
      }



 
        //return redirect()->route('oficina')->with('success','Creado');
       

    }

    public function modifica(Request $request)
    {
        //

        $personal = Personal::findOrFail($request->id);           
        $personal->apellidos = $request->apellidos;
        $personal->nombres = $request->nombres;

        $personal->email = $request->email;

        $personal->dni = $request->dni;
        
        $personal->spp = $request->spp;

        $personal->r4 = $request->r4;
        $personal->r5 = $request->r5;
        $personal->fecha_nacimiento = $request->fecha_nacimiento;
        $personal->idcargo = $request->cargos;
        $personal->idafp = $request->afp;
        $personal->idonp = $request->onp;
        $personal->fecha_ingreso = $request->fecha_ingreso;

        $personal->device_user_id = $request->device_user_id;
        $personal->remuneracion_mensual = $request->remuneracion_mensual;

        $personal->ip_address = $request->ip_address;        
        $personal->tipo_contrato = $request->tipo_contrato;
        $personal->mac_address = $request->mac_address;

        
        $personal->obra_id = $request->obra_id;     
        $personal->android_id = $request->android_id;    


        $personal->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    public function excel()
    {
        //$cargo = Personal::all()->toArray();       
        //return array_reverse($cargo); 

                // Incluye las relaciones con 'cargo', 'afp', y 'onp'
            $personal = Personal::with(['cargo', 'afp', 'onp'])
            ->get()
            ->toArray()         
            ;
            return array_reverse($personal); 

            // Retorna los datos en orden inverso
            
    }
    /**
     * Display the specified resource.
     */
 
     public function show($id)
            {
                // Cargar el modelo Personal con las relaciones activadas
                $personal = Personal::with(['cargo', 'afp', 'onp']) // Cambia estos nombres por los nombres reales de tus relaciones
                    ->where('id', $id)
                    ->first(); // Usa first() para obtener un solo registro

                if ($personal) {
                    // Convertir el modelo a un array
                    $personalArray = $personal->toArray();

                    // Aplicar array_reverse al array resultante
                    $reversedArray = array_reverse($personalArray);

                    return response()->json($reversedArray);
                } else {
                    // Manejar el caso en el que el registro no existe
                    return response()->json(['error' => 'Personal not found'], 404);
                }
            }

            public function showdni($id)
            {
                // Cargar el modelo Personal con las relaciones activadas
                $personal = Personal::with(['cargo', 'afp', 'onp']) // Cambia estos nombres por los nombres reales de tus relaciones
                    ->where('dni', $id)
                    ->first(); // Usa first() para obtener un solo registro

                if ($personal) {
                    // Convertir el modelo a un array
                    $personalArray = $personal->toArray();

                    // Aplicar array_reverse al array resultante
                    $reversedArray = array_reverse($personalArray);

                    return response()->json($reversedArray);
                } else {
                    // Manejar el caso en el que el registro no existe
                    return response()->json(['error' => 'Personal not found'], 404);
                }
            }



    /**
     * Show the form for editing the specified resource.
     */




    

   


     public function destroy(Request $request)
     {
       
        $personal = Personal::destroy($request->id);       
        return $personal;
                 
        
     }

     public function obtenerDni(Request $request)
     {
         try {
             // Configura las opciones de contexto para la solicitud HTTP
             $options = [
                 'http' => [
                     'method' => 'GET',
                     'header' => 'Content-type: application/json',
                 ],
             ];
 
             // Crea el contexto de la solicitud
             $context = stream_context_create($options);
             $TOKEN = env('TOKEN');
 
             // Realiza la solicitud HTTP y obtén la respuesta
             
             $response = file_get_contents('https://apiperu.dev/api/dni/'.$request->id.'?api_token='.$TOKEN , false, $context);
 
             if ($response === false) {
                 // Maneja el error de la solicitud
                 return response()->json(['error' => 'Error en la solicitud'], 500);
             }
 
             // Decodifica la respuesta JSON
             $data = json_decode($response, true);
 
             // Procesa los datos aquí
             return response()->json($data);
         } catch (\Exception $e) {
             // Maneja los errores aquí
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }


     public function obtenerDetallePersonal($dni)
     {
         // Obtener el personal con su detalle_planilla relacionado
         $personal = Personal::with('detalleplanilla.planilla') // Asegúrate de que la relación esté definida en el modelo Personal
             ->where('dni', $dni)
             ->first();

         if ($personal) {
             return response()->json($personal);
         } else {
             return response()->json(['error' => 'Personal no encontrado'], 404);
         }
     }


}
