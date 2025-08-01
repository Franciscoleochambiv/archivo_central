<?php

namespace App\Http\Controllers;

use App\Models\Metas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MetasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            // Define el número de elementos por página
            $perPage = 10;

            /*

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Construye la consulta base
            $query = Metas::query()
            ->orderBy('id', 'desc');

            // Si hay un término de búsqueda, agregar un filtro a la consulta
            if ( strlen($searchTerm) >= 3) {
            $query->where('descripcion', 'LIKE', "%{$searchTerm}%");
            }

            // Obtén los elementos paginados
            $metas = $query->paginate($perPage);

            */

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Genera una clave de caché única basada en el término de búsqueda y la paginación
            $cacheKey = "metas_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
            
            // Establece la duración del caché
            $cacheDuration = 60*480; // 60 minutos

            // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
            $metas = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
                // Construye la consulta base
                $query = Metas::query()
                    ->orderBy('id', 'desc');

                // Si hay un término de búsqueda, agregar un filtro a la consulta
                if (strlen($searchTerm) >= 3) {
                    $query->where('descripcion', 'LIKE', "%{$searchTerm}%");
                }

                // Obtén los elementos paginados
                return $query->paginate($perPage);
            });






            // Revertir el orden de los elementos
            $data = $metas->items();
            //$data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $metas = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $metas->total(),
                $metas->perPage(),
                $metas->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $metas como JSON
            return response()->json($metas);
    }

    public function excel()
    {
        $metas = Metas::all()->toArray();       
        return array_reverse($metas); 
    }

    

    public function buscar(Request $request)
    {
        $query = $request->input('q');

        if ($query) {
            $metas = Metas::where('codigo', 'like', '%' . $query . '%')->take(5)->get();
            return response()->json($metas);
        }

        return response()->json([], 404);
    }

    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $meta = new Metas;        
        $meta->codigo = $request->codigo;
        $meta->descripcion = $request->descripcion;        
        $meta->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $metas= Metas::select('metas.id', 'metas.codigo', 'metas.descripcion' ) // Alias para el campo descripcion
        ->where('metas.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($metas);
    }

    public function showmeta($id)
    {
        //
        $metas= Metas::select('metas.id', 'metas.codigo', 'metas.descripcion' ) // Alias para el campo descripcion
        ->where('metas.codigo', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($metas);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
    {
        //

        $meta = Metas::findOrFail($request->id);

        $meta->codigo = $request->codigo;
        $meta->descripcion = $request->descripcion;
        $meta->save();
  
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');
    }

   


     public function destroy(Request $request)
     {
       
        $meta = Metas::destroy($request->id);       
        return $meta;
                 
        
     }
   
}
