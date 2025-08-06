<?php

namespace App\Http\Controllers;
use App\Models\Oficina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OficinaController extends Controller
{
    public function index(Request $request)
    {
           
            $perPage = 10;

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Genera una clave de caché única basada en el término de búsqueda y la paginación
            $cacheKey = "oficina_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
            
            // Establece la duración del caché
            $cacheDuration = 60*480; // 60 minutos

            // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
            $oficinas = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
                // Construye la consulta base
                $query = Oficina::query()
                    ->orderBy('id', 'desc');

                // Si hay un término de búsqueda, agregar un filtro a la consulta
                if (strlen($searchTerm) >= 3) {
                    $query->where('nombre', 'LIKE', "%{$searchTerm}%");
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

    public function excel()
    {
        $oficinas = Oficina::all()->toArray();       
        return array_reverse($oficinas); 
    }

    

    public function buscar(Request $request)
    {
        $query = $request->input('q');

        if ($query) {
            $oficinas = Oficina::where('nombre', 'like', '%' . $query . '%')->take(5)->get();
            return response()->json($oficinas);
        }

        return response()->json([], 404);
    }

    

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $oficina = new Oficina;        
        $oficina->entidad_id =1;
        $oficina->nombre = $request->nombre;        
        $oficina->seccion = $request->seccion;        
        $oficina->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $oficinas= Oficina::select('oficinas.id', 'oficinas.entidad_id','oficinas.nombre', 'oficinas.seccion' ) // Alias para el campo descripcion
        ->where('oficinas.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($oficinas);
    }

    public function showmeta($id)
    {
        //
        $oficinas= Oficina::select('oficinas.id', 'oficinas.entidad_id', 'oficinas.nombre','oficinas.seccion' ) // Alias para el campo descripcion
        ->where('oficinas.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($oficinas);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
    {
        //

        $oficina = Oficina::findOrFail($request->id);

        $oficina->entidad_id = 1;
        $oficina->nombre = $request->nombre;
        $oficina->seccion = $request->seccion;

        $oficina->save();
  
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');
    }



     public function destroy(Request $request)
     {
       
        $oficina = Oficina::destroy($request->id);       
        return $oficina;
                 
        
     }

}
