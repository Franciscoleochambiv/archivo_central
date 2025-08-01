<?php
namespace App\Http\Controllers;

use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class TokenAccesoController extends Controller
{
    public function solicitar(Request $request)
    {
        $request->validate([
            'dni' => 'required',
            'email' => 'required|email',
        ]);

        $user = Personal::where('dni', $request->dni)
                    ->where('email', $request->email)
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'DNI o correo no válidos'], 404);
        }

        $token = Str::random(40);
        Cache::put("token_{$token}", $user->dni, now()->addMinutes(10));

        // Envía el correo
        Mail::raw("Tu token de acceso es: $token", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Token de acceso a reportes');
        });

        return response()->json(['message' => 'Token enviado al correo.']);
    }

    public function validar(Request $request)
    {
        $request->validate(['token' => 'required']);

        $dni = Cache::pull("token_{$request->token}");

        if (!$dni) {
            return response()->json(['message' => 'Token inválido o expirado'], 401);
        }

        return response()->json(['dni' => $dni]);
    }
}
