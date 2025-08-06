<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entidad;

class EntidadController extends Controller
{
    //
     public function index()
    {
        return Entidad::all();
    }

    // Guardar una nueva entidad
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:100',
        ]);

        $entidad = Entidad::create($request->only('nombre', 'tipo'));

        return response()->json($entidad, 201);
    }

    // Obtener una entidad por ID
    public function show($id)
    {
        $entidad = Entidad::findOrFail($id);
        return response()->json($entidad);
    }
}
