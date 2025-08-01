<?php

namespace App\Http\Controllers;


use App\Models\Onp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OnpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            // Define el número de elementos por página
            $perPage = 10;

            /*metodo sion usar el cache

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Construye la consulta base
            $query = Onp::query()
            ->orderBy('id', 'desc');

            // Si hay un término de búsqueda, agregar un filtro a la consulta
            if ( strlen($searchTerm) >= 3) {
            $query->where('nombre_onp', 'LIKE', "%{$searchTerm}%");
            }

            // Obtén los elementos paginados
            $onps = $query->paginate($perPage);

            */
            
            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Genera una clave de caché única basada en el término de búsqueda y la paginación
            $cacheKey = "onps_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
            
            // Establece la duración del caché
            $cacheDuration = 60*480; // 60 minutos

            // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
            $onps = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
                // Construye la consulta base
                $query = Onp::query()
                    ->orderBy('id', 'desc');

                // Si hay un término de búsqueda, agregar un filtro a la consulta
                if (strlen($searchTerm) >= 3) {
                    $query->where('nombre_onp', 'LIKE', "%{$searchTerm}%");
                }

                // Obtén los elementos paginados
                return $query->paginate($perPage);
            });





            // Revertir el orden de los elementos
            $data = $onps->items();
            //$data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $onps = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $onps->total(),
                $onps->perPage(),
                $onps->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $afps como JSON
            return response()->json($onps);
    }

    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $onp = new Onp;                
        $onp->nombre_onp = $request->nombre_onp;        
        $onp->fondo = $request->fondo;                
        $onp->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    public function excel()
    {
        $onp = Onp::all()->toArray();       
        return array_reverse($onp); 
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $onp= Onp::select('onp.id', 'onp.nombre_onp', 'onp.fondo') // Alias para el campo descripcion
        ->where('onp.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($onp);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
    {
        //

        $onp = Onp::findOrFail($request->id);

        
        $onp->nombre_onp = $request->nombre_onp;
        $onp->fondo = $request->fondo;        
        $onp->save();
  
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The put successfully added');
    }

   


     public function destroy(Request $request)
     {
       
        $onp = Onp::destroy($request->id);       
        return $onp;
                 
        
     }

}
