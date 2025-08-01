<?php

namespace App\Http\Controllers;

use App\Models\Movimientos;
use Illuminate\Http\Request;

class MovimientosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        //$movimientos = Movimientos::all()->toArray();       
        //return array_reverse($movimientos); 
        
        
        $movimientos_gasolina = Movimientos::join('metas', 'movimientos.id_meta', '=', 'metas.id')
        ->select('movimientos.id', 'movimientos.userid', 'movimientos.id_meta', 'movimientos.cod_meta', 
                'movimientos.meta', 'movimientos.tipo_doc','movimientos.cantidad','movimientos.tipo_combustible','movimientos.nro_doc',
                'movimientos.detalle', 'movimientos.fecha' ) // Alias para el campo descripcion    
        ->where('movimientos.tipo_combustible', '=', 0) // Filtrar por tipo_combustible igual a 0 (gasolina)
        ->orderBy('movimientos.fecha', 'asc') // Ordenar por fecha ascendente
        ->get()
        ->toArray();

        $movimientos_petroleo= Movimientos::join('metas', 'movimientos.id_meta', '=', 'metas.id')
        ->select('movimientos.id', 'movimientos.userid', 'movimientos.id_meta', 'movimientos.cod_meta', 
                'movimientos.meta', 'movimientos.tipo_doc','movimientos.cantidad','movimientos.tipo_combustible','movimientos.nro_doc',
                'movimientos.detalle', 'movimientos.fecha' ) // Alias para el campo descripcion    
        ->where('movimientos.tipo_combustible', '=', 1) // Filtrar por tipo_combustible igual a 0 (gasolina)
        ->orderBy('movimientos.fecha', 'asc') // Ordenar por fecha ascendente
        ->get()
        ->toArray();


        $movimientos_combinados = [
            0 => $movimientos_gasolina,
            1 => $movimientos_petroleo
        ];

        //$movimientos_combinados = array_merge($movimientos_gasolina, $movimientos_petroleo);

       return array_reverse($movimientos_combinados);


       // return array_reverse($movimientos);



    }


    public function bmovi($id)
    {
        // Busca el movimiento por ID
        $movimiento= Movimientos::join('metas', 'movimientos.id_meta', '=', 'metas.id')
        ->select('movimientos.id', 'movimientos.userid', 'movimientos.id_meta', 'movimientos.cod_meta', 
                  'movimientos.meta', 'movimientos.tipo_doc','movimientos.cantidad','movimientos.tipo_combustible','movimientos.nro_doc',
                  'movimientos.detalle', 'movimientos.fecha' ) // Alias para el campo descripcion
        ->where('movimientos.id', '=', $id)        
        ->get()
        ->toArray();

        return array_reverse($movimiento);
    }


    public function bmetas(Request $request)
    {
     $movimientos_gasolina = Movimientos::join('metas', 'movimientos.id_meta', '=', 'metas.id')
    ->select('movimientos.id', 'movimientos.userid', 'movimientos.id_meta', 'movimientos.cod_meta', 
              'movimientos.meta', 'movimientos.tipo_doc','movimientos.cantidad','movimientos.tipo_combustible','movimientos.nro_doc',
              'movimientos.detalle', 'movimientos.fecha' ) // Alias para el campo descripcion
    ->where('movimientos.id_meta', '=', $request->id)
    ->where('movimientos.tipo_combustible', '=', 0) // Filtrar por tipo_combustible igual a 0 (gasolina)
    ->orderBy('movimientos.fecha', 'asc') // Ordenar por fecha ascendente
    ->get()
    ->toArray();

    $movimientos_petroleo = Movimientos::join('metas', 'movimientos.id_meta', '=', 'metas.id')
    ->select('movimientos.id', 'movimientos.userid', 'movimientos.id_meta', 'movimientos.cod_meta', 
              'movimientos.meta', 'movimientos.tipo_doc','movimientos.cantidad','movimientos.tipo_combustible','movimientos.nro_doc',
              'movimientos.detalle', 'movimientos.fecha' ) // Alias para el campo descripcion
    ->where('movimientos.id_meta', '=', $request->id)
    ->where('movimientos.tipo_combustible', '=', 1) // Filtrar por tipo_combustible igual a 0 (gasolina)
    ->orderBy('movimientos.fecha', 'asc') // Ordenar por fecha ascendente
    
    ->get()
    ->toArray();

    $movimientos_combinados = [
        0 => $movimientos_gasolina,
        1 => $movimientos_petroleo
    ];
   // $movimientos_combinados = array_merge($movimientos_gasolina, $movimientos_petroleo);

    return array_reverse($movimientos_combinados);
     //return array_reverse($movimientos);

    }
    

    /**
     * 
     * 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        //$request->validate([
        ///    'nombre'=> 'required|min:1',
        //    'descripcion'=> 'required|min:1'
        // ]);
  
         $movimientos = new Movimientos;
         $movimientos->id_meta = $request->id_meta;
         $movimientos->cod_meta = $request->cod_meta;
         $movimientos->meta = $request->meta;
         $movimientos->tipo_doc = $request->tipo_doc;
         $movimientos->tipo_combustible = $request->tipo_combustible;
         $movimientos->nro_doc = $request->nro_doc;
         $movimientos->detalle = $request->detalle;
         $movimientos->userid = $request->userid;
         $movimientos->detalle = $request->detalle;
         $movimientos->cantidad = $request->cantidad;
         $movimientos->fecha = $request->fecha;
         $movimientos->save();
  
         //return redirect()->route('oficina')->with('success','Creado');
         return response()->json('The post successfully added');


    }


    public function modifica(Request $request)
    {
        //
        //$request->validate([
        ///    'nombre'=> 'required|min:1',
        //    'descripcion'=> 'required|min:1'
        // ]);

         //$id=$request->id;
         $movimientos = Movimientos::findOrFail($request->id);

         $movimientos->id_meta = $request->id_meta;
         $movimientos->cod_meta = $request->cod_meta;
         $movimientos->meta = $request->meta;
         $movimientos->tipo_doc = $request->tipo_doc;
         $movimientos->tipo_combustible = $request->tipo_combustible;
         $movimientos->nro_doc = $request->nro_doc;
         $movimientos->detalle = $request->detalle;
         $movimientos->userid = $request->userid;
         $movimientos->detalle = $request->detalle;
         $movimientos->cantidad = $request->cantidad;
         $movimientos->fecha = $request->fecha;
         $movimientos->save();
  
         //return redirect()->route('oficina')->with('success','Creado');
         return response()->json('The post successfully added');


    }


    public function saldos()
    {
        
        $ingresos_gasolina = (float)  Movimientos::where('tipo_doc', 1)
                                 ->where('tipo_combustible', 0)
                                 ->sum('cantidad');

        $ingresos_petroleo = (float)   Movimientos::where('tipo_doc', 1)
                                        ->where('tipo_combustible', 1)
                                        ->sum('cantidad');

        $egresos_gasolina = (float)  Movimientos::where('tipo_doc', 2)
                                        ->where('tipo_combustible', 0)
                                        ->sum('cantidad');

        $egresos_petroleo = (float) Movimientos::where('tipo_doc', 2)
                                        ->where('tipo_combustible', 1)
                                        ->sum('cantidad');

                                        
        
        $saldos_gasolina = (float)$ingresos_gasolina - (float)$egresos_gasolina;
        $saldos_petroleo = (float)$ingresos_petroleo - (float)$egresos_petroleo;

        // Crear un objeto con los resultados
        $resultado = [
            'ingresos_gasolina' => $ingresos_gasolina,
            'egresos_gasolina' => $egresos_gasolina,
            'ingresos_petroleo' => $ingresos_petroleo,
            'egresos_petroleo' => $egresos_petroleo,
            'saldos_gasolina' => $saldos_gasolina,
            'saldos_petroleo' => $saldos_petroleo,

            //'sal'=> $ingresos-$egresos
        ];

        return response()->json($resultado);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //



    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movimientos $movimientos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movimientos $movimientos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   


    public function destroy(Request $request)
    {
      
       $movimientos = Movimientos::destroy($request->id);       
       return $movimientos;
                
       
    }

}
