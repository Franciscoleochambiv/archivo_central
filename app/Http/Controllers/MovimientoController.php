<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimientos;

class MovimientoController extends Controller
{
    public function store(Request $request)
    {
        return Movimientos::create([
            'registro_documental_id' => $request->registro_documental_id,
            'tipo' => $request->tipo,
            'usuario' => $request->usuario,
            'comentario' => $request->comentario,
            'fecha' => $request->fecha ?? now(),
        ]);
    }

    public function index($registroId)
    {
        return Movimientos::where('registro_documental_id', $registroId)->get();
    }
}
