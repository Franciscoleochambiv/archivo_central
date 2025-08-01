<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentoAdjunto;

class DocumentoAdjuntoController extends Controller
{
    //
    public function store(Request $request)
    {
        $archivo = $request->file('archivo');
        $ruta = $archivo->store('documentos');

        return DocumentoAdjunto::create([
            'registro_documental_id' => $request->registro_documental_id,
            'nombre' => $archivo->getClientOriginalName(),
            'ruta' => $ruta,
        ]);
    }

    public function index($registroId)
    {
        return DocumentoAdjunto::where('registro_documental_id', $registroId)->get();
    }
}
