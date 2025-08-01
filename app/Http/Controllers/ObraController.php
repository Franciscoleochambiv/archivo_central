<?php

namespace App\Http\Controllers;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ObraController extends Controller
{
    public function index(Request $request)
    {
            // Define el número de elementos por página
            $perPage = 10;

       

           // Obtén el término de búsqueda desde la consulta
           $searchTerm = $request->input('q');

           // Genera una clave de caché única basada en el término de búsqueda y la paginación
           $cacheKey = "obras_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
           
           // Establece la duración del caché
           $cacheDuration = 60*480; // 60 minutos

           // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
           $obras = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
               // Construye la consulta base
               $query = Obra::query()
                   ->orderBy('id', 'desc');

               // Si hay un término de búsqueda, agregar un filtro a la consulta
               if (strlen($searchTerm) >= 3) {
                   $query->where('nombre', 'LIKE', "%{$searchTerm}%");
               }

               // Obtén los elementos paginados
               return $query->paginate($perPage);
           });




            // Revertir el orden de los elementos
            $data = $obras->items();
           // $data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $obras = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $obras->total(),
                $obras->perPage(),
                $obras->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $afps como JSON
            return response()->json($obras);
    }

    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $obra = new Obra;                
        $obra->nombre = $request->nombre;                
        $obra->latitud = $request->latitud;                
        $obra->longitud = $request->longitud;                
        $obra->radio = $request->radio;                
        $obra->direccion = $request->direccion;                
        $obra->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    public function excel()
    {
        $obra = Obra::all()->toArray();       
        return array_reverse($obra); 
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $obra= Obra::select('obras.id', 'obras.nombre','obras.latitud','obras.longitud','obras.radio','obras.direccion') // Alias para el campo descripcion
        ->where('obras.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($obra);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
        {
            try {
                // Buscar la obra por ID, lanza una excepción si no se encuentra
                $obra = Obra::findOrFail($request->id);

                // Actualizar los valores de la obra con los datos del request
                $obra->nombre = $request->nombre;    
                $obra->latitud = $request->latitud;    
                $obra->longitud = $request->longitud;    
                $obra->radio = $request->radio;    
                $obra->direccion = $request->direccion;    

                // Guardar los cambios
                $obra->save();

                // Responder con éxito
                return response()->json(['message' => 'The obra was successfully updated'], 200);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Error si la obra no fue encontrada
                return response()->json(['error' => 'Obra not found'], 404);
            } catch (\Exception $e) {
                // Manejar cualquier otro error
                return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
            }
        }


   


        public function destroy(Request $request)
        {
            try {
                // Intenta eliminar la obra usando el ID del request
                $deleted = Obra::destroy($request->id);
        
                // Verifica si la eliminación tuvo éxito
                if ($deleted) {
                    return response()->json(['message' => 'Obra eliminada correctamente'], 200);
                } else {
                    // Si no se eliminó, devuelve un mensaje de error
                    return response()->json(['error' => 'No se encontró la obra o no pudo ser eliminada'], 404);
                }
            } catch (\Exception $e) {
                // Captura cualquier excepción y devuelve una respuesta de error
                return response()->json([
                    'error' => 'Ocurrió un error al intentar eliminar la obra',
                    'details' => $e->getMessage(),
                ], 500);
            }
        }

}
