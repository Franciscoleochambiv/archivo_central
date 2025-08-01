<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uit;

class UitController extends Controller
{
    //
     public function getByYear($anio)
    {
        $uit = Uit::where('anio', $anio)->first();

        if (!$uit) {
            return response()->json(['message' => 'UIT no encontrada para el año ' . $anio], 404);
        }

        return response()->json(['valor' => $uit->valor]);
    }
}
