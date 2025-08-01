<?php

namespace App\Http\Controllers;

use App\Models\Afp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class AfpController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
            // Define el número de elementos por página
            $perPage = 10;


            /* //metodo 1

            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Construye la consulta base
            $query = Afp::query()
            ->orderBy('id', 'desc');

            // Si hay un término de búsqueda, agregar un filtro a la consulta
            if ( strlen($searchTerm) >= 3) {
            $query->where('nombre_afp', 'LIKE', "%{$searchTerm}%");
            }

            // Obtén los elementos paginados
            $afps = $query->paginate($perPage);

          */

          //METODO uSANDO EL CACHE
            // Obtén el término de búsqueda desde la consulta
            $searchTerm = $request->input('q');

            // Genera una clave de caché única basada en el término de búsqueda y la paginación
            $cacheKey = "afps_search_".md5($searchTerm)."_page_" . $request->get('page', 1);
            
            // Establece la duración del caché
            $cacheDuration = 60*480; // 60 minutos

            // Obtén el resultado de la caché o ejecuta la consulta si no está en caché
            $afps = Cache::remember($cacheKey, $cacheDuration, function () use ($searchTerm, $perPage) {
                // Construye la consulta base
                $query = Afp::query()
                    ->orderBy('id', 'desc');

                // Si hay un término de búsqueda, agregar un filtro a la consulta
                if (strlen($searchTerm) >= 3) {
                    $query->where('nombre_afp', 'LIKE', "%{$searchTerm}%");
                }

                // Obtén los elementos paginados
                return $query->paginate($perPage);
            });




            // Revertir el orden de los elementos
            $data = $afps->items();
            //$data = array_reverse($data);

            // Crear un nuevo objeto LengthAwarePaginator con los datos revertidos
            $afps = new \Illuminate\Pagination\LengthAwarePaginator(
                $data,
                $afps->total(),
                $afps->perPage(),
                $afps->currentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Retornar la variable $afps como JSON
            return response()->json($afps);
    }

    public function excel()
    {
        $afp = Afp::all()->toArray();       
        return array_reverse($afp); 
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

        $afp = new Afp;                
        $afp->nombre_afp = $request->nombre_afp;        
        $afp->comision_fija = $request->comision_fija;        
        $afp->prima = $request->prima;        
        $afp->comision_porcentual = $request->comision_porcentual;        
        $afp->save();
 
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The post successfully added');


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $afp= Afp::select('afp.id', 'afp.nombre_afp', 'afp.comision_fija', 'afp.prima','afp.comision_porcentual') // Alias para el campo descripcion
        ->where('afp.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($afp);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function modifica(Request $request)
    {
        //

        $afp = Afp::findOrFail($request->id);

        
        $afp->nombre_afp = $request->nombre_afp;
        $afp->comision_fija = $request->comision_fija;
        $afp->prima = $request->prima;
        $afp->comision_porcentual = $request->comision_porcentual;
        $afp->save();
  
        //return redirect()->route('oficina')->with('success','Creado');
        return response()->json('The put successfully added');
    }

   


     public function destroy(Request $request)
     {
       
        $afp = Afp::destroy($request->id);       
        return $afp;
                 
        
     }
   
}
