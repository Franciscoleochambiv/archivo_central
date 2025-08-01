<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\PagosOnline;

class PagosOnlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function procesarPago(Request $request)
   {
    // Validar que el monto sea 200
    $monto = $request->input('monto');
    if ($monto != 200) {
        return response()->json([
            'error' => 'El monto debe ser exactamente 200 para proceder con el pago.'
        ], 400);
    }

    // Llamada a la API de Go para generar el token
    $response = $this->generarTokenDePago($request);

    // Verifica si la respuesta es exitosa y almacena los detalles del pago
    if ($response->status() == 200) {
        $this->registrarPago($monto, $response->json()['token']);
        
        // Retorna éxito en el pago
        return response()->json([
            'mensaje' => 'Pago realizado con éxito.',
            'token' => $response->json()['token']
        ]);
    }

    // Si hubo un error en la llamada a la API de Go
    return response()->json([
        'error' => 'No se pudo procesar el pago.'
    ], 500);
}


public function generarTokenDePago(Request $request)
{
    try {
        // Valida los datos que vienen en el request
        $request->validate([
            'telefono' => 'required|digits:9', 
            'otp' => 'required|string',
            'monto' => 'required|numeric',
            'dni' => 'required|digits:8', 
        ]);

        // Prepara la URL de la API de tokenización
        $url = 'https://pyape.facturameya.online/tokenize';
        
        // Configura los datos para enviar en la tokenización
        $data = [
            'number_phone' => $request->input('telefono'),
            'otp' => $request->input('otp'),
            'amount' => (string) $request->input('monto'), // Convertir amount a string
            'dni' => $request->input('dni'),
        ];

        // Convierte los datos a JSON
        $jsonData = json_encode($data);

        // Inicializa cURL para la solicitud de tokenización
        $ch = curl_init($url);

        // Configura las opciones de cURL para tokenización
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Asegúrate de que sea JSON
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Enviar los datos como JSON

        // Ejecuta la solicitud y guarda la respuesta
        $response = curl_exec($ch);

        // Verifica si la solicitud de cURL ha fallado
        if ($response === false) {
            $error = curl_error($ch);
            return response()->json(['error' => "Error en cURL: " . $error], 500);
        }

        // Decodifica la respuesta de la API de tokenización
        $decodedResponse = json_decode($response, true);

        // Verifica si hubo un error en la API de tokenización
        if (isset($decodedResponse['code']) && $decodedResponse['code'] === 'YPCHK0010') {
            // Hubo un error en la tokenización, mostrar mensaje al usuario
            return response()->json($decodedResponse);
        }

        // Retornar la respuesta exitosa
        return response()->json($decodedResponse);

    } catch (\Exception $e) {
        // Maneja cualquier error y lo envía como respuesta
        return response()->json(['error' => $e->getMessage()], 500);
    } finally {
        // Asegúrate de cerrar el cURL siempre, incluso si hay un error
        if (isset($ch)) {
            curl_close($ch);
        }
    }
}







public function generarCargo(Request $request)
{
    try {
        // Valida los datos que vienen en el request
        $request->validate([            
            'amount'=>  'required|numeric',
            'currency_code'=>  'required|string',
            'email'=>  'required|string',
            'source_id'=> 'required|string',
            'description'=> 'required|string',
            'dni'=>  'required|numeric',
        ]);
         // Prepara la URL de la API de tokenización
         //se cambio el url para t¿peticon local
            //$url = 'http://localhost:8080/charge';
            $url = 'https://pyape.facturameya.online/charge';
          
            // Configura los datos para enviar en la tokenización
            $data = [

                'amount' => $request->input('amount'),
                'currency_code'=>$request->input('currency_code'),
                'email'=>$request->input('email'),
                'source_id'=>$request->input('source_id'),
                'description'=>$request->input('description'),
                'dni' => $request->input('dni'),
            ];

            // Convierte los datos a JSON
            $jsonData = json_encode($data);

            // Inicializa cURL para la solicitud de tokenización
            $ch = curl_init($url);

            // Configura las opciones de cURL para tokenización
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json', // Asegúrate de que sea JSON
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Enviar los datos como JSON

            // Ejecuta la solicitud y guarda la respuesta
            $response = curl_exec($ch);

            // Verifica si la solicitud de cURL ha fallado
            if ($response === false) {
                $error = curl_error($ch);
                echo "Error en cURL: " . $error;
            } else {
                // Decodifica la respuesta de la API de tokenización
                $decodedResponse = json_decode($response, true);

                return response()->json($decodedResponse);

                /*
                // Verifica si hubo un error en la API de tokenización
                if (isset($decodedResponse['code']) && $decodedResponse['code'] === 'YPCHK0010') {
                    // Hubo un error en la tokenización, mostrar mensaje al usuario
                    return response()->json($decodedResponse);
                } else {
                    // No hubo error, ahora realizar la solicitud a la API de cargos

                    
                }
                    */
            }

            // Cierra el cURL de tokenización
            curl_close($ch);


    } catch (\Exception $e) {
        // Maneja cualquier error y lo envía como respuesta
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



public function registrarPago(Request $request)
{
  
     // Obtén los datos del cuerpo de la solicitud
    $monto = $request->input('monto');
    $token = $request->input('token');
    $usuario_id = $request->input('usuario_id');

    // Guarda el pago en la base de datos
    try {
        PagosOnline::create([
            'usuario_id' => $usuario_id, // ID del usuario autenticado
            'monto' => $monto,
            'token' => $token,
            'fecha_pago' => now(), // Fecha actual del pago
            'fecha_vigencia' => now()->addDays(30), // Vigencia de 30 días
            'estado' => 'pagado' // Estado del pago
    ]);

        return response()->json(['message' => 'Pago registrado correctamente']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al registrar el pago'], 500);
    }
}


public function mostrarBotonDePago()
{
    $ultimoPago =  PagosOnline::query()
        ->orderBy('fecha_pago', 'desc')
        ->first();

        if ($ultimoPago && \Carbon\Carbon::parse($ultimoPago->fecha_vigencia)->isFuture()) {
        // El botón no se activa si la vigencia no ha caducado
        return response()->json([
            'activo' => false,
            'mensaje' => 'El botón de pago no está disponible. El pago es válido hasta ' . $ultimoPago->fecha_vigencia,
        ]);
    }

    // El botón está activo si no hay pagos recientes o si ya caducó la vigencia
    return response()->json([
        'activo' => true,
        'mensaje' => 'El botón de pago está disponible para realizar el pago.'
    ]);
}


}
