<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarTokenDeIngreso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenValido = env('ASISTENCIA_CLIENT_TOKEN');
        $tokenRecibido = $request->cookie('token_ingreso'); // o header

        if ($tokenRecibido !== $tokenValido) {
            return response('Acceso denegado.', 403);
        }

        return $next($request);
    }
}
