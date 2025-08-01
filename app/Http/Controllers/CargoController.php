<?php

namespace App\Http\Controllers;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CargoController extends Controller
{
    public function index(Request $request)
    {
            // Define el número de elementos por página
            $perPage = 10;

            /* Metodo 1  sin usar el Cache

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Construye la consulta base
            $query = Cargo::query()
            ->orderBy('id', 'desc');
            

            // Si hay un término de búsqueda, agregar un filtro a la consulta
            if ( strlen($searchTerm) >= 3) {
            $query->where('nombre_cargo', 'LIKE', "%{$searchTerm}%");
            }

            // Obtén los elementos paginados
            $cargos = $query->paginate($perPage);

          */

           // Obtén el término de búsqueda desde la consulta
           $searchTerm = $request->input('q');

           // Genera una clave de caché única basada en el término de búsqueda y la paginación
           $cacheKey = "cargos_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
           
           // Establece la duración del caché
           $cacheDuration = 60*480; // 60 minutos

           // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
           $cargos = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
               // Construye la consulta base
               $query = Cargo::query()
                   ->orderBy('id', 'desc');

               // Si hay un término de búsqueda, agregar un filtro a la consulta
               if (strlen($searchTerm) >= 3) {
                   $query->where('nombre_cargo', 'LIKE', "%{$searchTerm}%");
               }

               // Obtén los elementos paginados
               return $query->paginate($perPage);
           });




            // Revertir el orden de los elementos
            $data = $cargos->items();
           // $data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $cargos = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $cargos->total(),
                $cargos->perPage(),
                $cargos->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $afps como JSON
            return response()->json($cargos);
    }

    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $cargo = new Cargo;                
        $cargo->nombre_cargo = $request->nombre_cargo;                
        $cargo->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    public function excel()
    {
        $cargo = Cargo::all()->toArray();       
        return array_reverse($cargo); 
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $cargo= Cargo::select('cargo.id', 'cargo.nombre_cargo') // Alias para el campo descripcion
        ->where('cargo.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($cargo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
    {
        //

        $cargo = Cargo::findOrFail($request->id);

        
        $cargo->nombre_cargo = $request->nombre_cargo;    
        $cargo->save();
  
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The put successfully added');
    }

   


     public function destroy(Request $request)
     {
       
        $cargo = Cargo::destroy($request->id);       
        return $cargo;
                 
        
     }

}
