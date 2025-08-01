<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordExportController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\TokenAccesoController;

use App\Http\Controllers\ObraController;

use App\Http\Controllers\Api\UitController;

use App\Http\Controllers\RegistroDocumentalController;
use App\Http\Controllers\DocumentoAdjuntoController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\EntidadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('expedientes', [RegistroDocumentalController::class, 'index']);
Route::post('expedientes', [RegistroDocumentalController::class, 'store']);

Route::get('expedientes/buscar', [RegistroDocumentalController::class, 'buscar']);

//api para la edicion
Route::get('expedientes/{id}', [RegistroDocumentalController::class, 'show']);
Route::put('expedientes/{id}', [RegistroDocumentalController::class, 'update']);

Route::delete('expediente/{id}', [RegistroDocumentalController::class, 'destroy']);


Route::get('expedientes/oficina/{id}', [RegistroDocumentalController::class, 'porOficina']);




Route::get('expedientes/oficina/{id}/conteo', [RegistroDocumentalController::class, 'conteo']);




//reportes
// Reporte general de expedientes
Route::get('/reportes/expedientes', [RegistroDocumentalController::class, 'reporteExpedientes']);

// Reporte por entidad
Route::get('/reportes/entidad', [RegistroDocumentalController::class, 'reportePorEntidad']);

// Reporte por oficina
Route::get('/reportes/oficina', [RegistroDocumentalController::class, 'reportePorOficina']);

// Reporte por estado archivador
Route::get('/reportes/estado-archivador', [RegistroDocumentalController::class, 'reportePorEstadoArchivador']);

// Reporte por ubicación actual
Route::get('/reportes/ubicacion-actual', [RegistroDocumentalController::class, 'reportePorUbicacionActual']);







Route::get('expedientes/{id}/documentos', [DocumentoAdjuntoController::class, 'index']);
Route::post('documentos', [DocumentoAdjuntoController::class, 'store']);

Route::get('expedientes/{id}/movimientos', [MovimientoController::class, 'index']);
Route::post('movimientos', [MovimientoController::class, 'store']);

Route::get('entidades', [EntidadController::class, 'index']);




//Route::get('/metas', 'App\Http\Controllers\OficinaController@index');
Route::get('/metas', 'App\Http\Controllers\OficinaController@buscar');

Route::get('/oficinast', 'App\Http\Controllers\OficinaController@index');

Route::get('/oficinaexcel', 'App\Http\Controllers\OficinaController@excel');

Route::delete('/oficina/{id}', 'App\Http\Controllers\OficinaController@destroy');
Route::post('/oficina', 'App\Http\Controllers\OficinaController@store');
Route::get('/oficina/{id}', 'App\Http\Controllers\OficinaController@show');
Route::get('/metacod/{id}', 'App\Http\Controllers\OficinaController@showmeta');
Route::put('/oficinamodifica/{id}', 'App\Http\Controllers\OficinaController@modifica');








Route::get('/afpst', 'App\Http\Controllers\AfpController@index');
Route::get('/afpexcel', 'App\Http\Controllers\AfpController@excel');
Route::delete('/afp/{id}', 'App\Http\Controllers\AfpController@destroy');
Route::post('/afp', 'App\Http\Controllers\AfpController@store');
Route::get('/afp/{id}', 'App\Http\Controllers\AfpController@show');
Route::put('/afpmodifica/{id}', 'App\Http\Controllers\AfpController@modifica');


Route::get('/onpst', 'App\Http\Controllers\OnpController@index');
Route::get('/onpexcel', 'App\Http\Controllers\OnpController@excel');
Route::delete('/onp/{id}', 'App\Http\Controllers\OnpController@destroy');
Route::post('/onp', 'App\Http\Controllers\OnpController@store');
Route::get('/onp/{id}', 'App\Http\Controllers\OnpController@show');
Route::put('/onpmodifica/{id}', 'App\Http\Controllers\OnpController@modifica');


Route::get('/cargost', 'App\Http\Controllers\CargoController@index');
Route::get('/cargoexcel', 'App\Http\Controllers\CargoController@excel');
Route::delete('/cargo/{id}', 'App\Http\Controllers\CargoController@destroy');
Route::post('/cargo', 'App\Http\Controllers\CargoController@store');
Route::get('/cargo/{id}', 'App\Http\Controllers\CargoController@show');
Route::put('/cargomodifica/{id}', 'App\Http\Controllers\CargoController@modifica');


Route::get('/personalst', 'App\Http\Controllers\PersonalController@index');
Route::get('/personalexcel', 'App\Http\Controllers\PersonalController@excel');
Route::delete('/personal/{id}', 'App\Http\Controllers\PersonalController@destroy');
Route::post('/personal', 'App\Http\Controllers\PersonalController@store');
Route::get('/personal/{id}', 'App\Http\Controllers\PersonalController@show');
Route::put('/personalmodifica/{id}', 'App\Http\Controllers\PersonalController@modifica');
Route::get('/personaldni/{id}', 'App\Http\Controllers\PersonalController@showdni');

Route::get('/personaldetalle/{id}', 'App\Http\Controllers\PersonalController@obtenerDetallePersonal');


Route::get('/dni/{id}', 'App\Http\Controllers\PersonalController@obtenerDni');



Route::get('/dnirm/{id}', 'App\Http\Controllers\PlanillaController@dni_rm');


Route::get('/planillast/{id}/{anio}', 'App\Http\Controllers\PlanillaController@index');





Route::get('/periodoexcel/{id}', 'App\Http\Controllers\PlanillaController@periodoexcel');

Route::get('/periodoexcelraci/{id}', 'App\Http\Controllers\PlanillaController@periodoexcelraci');


Route::get('/periodoexceltotal/{id}', 'App\Http\Controllers\PlanillaController@periodoexceltotal');




Route::get('/validarperiodo/{id}', 'App\Http\Controllers\PlanillaController@validarperiodo');

Route::get('/planillaexcel', 'App\Http\Controllers\PlanillaController@excel');

//Route::get('/planilladetalle_perido/{id}', 'App\Http\Controllers\PlanillaController@detalle_periodo');





Route::get('/planilladetalle_excel/{id}/{anio}', 'App\Http\Controllers\PlanillaController@detalle_excel');

 //const response = await axios.get('/api/planilladetalle_excel/'+this.selectedMonth);







Route::get('/planilladetalle_nombres/{id}', 'App\Http\Controllers\PlanillaController@detalle_nombres');

Route::get('/planilladetalle_txt/{id}', 'App\Http\Controllers\PlanillaController@detalle_txt');

Route::delete('/planilla/{id}', 'App\Http\Controllers\PlanillaController@destroy');
Route::post('/planilla', 'App\Http\Controllers\PlanillaController@store');



Route::post('/obtener_ia', 'App\Http\Controllers\PlanillaController@obtener_ia');
Route::post('/ia_datos', 'App\Http\Controllers\PlanillaController@ia_datos');



Route::get('/planilla/{id}', 'App\Http\Controllers\PlanillaController@show');


Route::put('/planillamodifica/{id}', 'App\Http\Controllers\PlanillaController@modifica');





Route::post('/movimientos', 'App\Http\Controllers\MovimientosController@store');
Route::get('/movimientos', 'App\Http\Controllers\MovimientosController@index');


Route::get('/movimientos/{id}', 'App\Http\Controllers\MovimientosController@bmetas');
Route::delete('/movimientos/{id}', 'App\Http\Controllers\MovimientosController@destroy');

Route::put('/movimientos/{id}', 'App\Http\Controllers\MovimientosController@modifica');



Route::get('/movi/{id}', 'App\Http\Controllers\MovimientosController@bmovi');



Route::get('/saldos', 'App\Http\Controllers\MovimientosController@saldos');



Route::get('/pagos/estado-boton', 'App\Http\Controllers\PagosOnlineController@mostrarBotonDePago');

Route::post('/pagos/procesar', 'App\Http\Controllers\PagosOnlineController@generarTokenDePago');

Route::post('/pagos/generarCargo', 'App\Http\Controllers\PagosOnlineController@generarCargo');
Route::post('/pagos/registrarPago', 'App\Http\Controllers\PagosOnlineController@registrarPago');




Route::post('/export-word', [WordExportController::class, 'generateDocument']);


Route::get('/zk/connect', 'App\Http\Controllers\ZKController@connectToDevice');
Route::get('/zk/attendances', 'App\Http\Controllers\ZKController@fetchAttendances');




Route::get('/asistencia/procesar', [AsistenciaController::class, 'procesarAsistencia']);

Route::post('/asistencia','App\Http\Controllers\AsistenciaController@store');

//Route::apiResource('obras', ObraController::class); editobra

Route::get('/obras', 'App\Http\Controllers\ObraController@index');
Route::post('/obras', 'App\Http\Controllers\ObraController@store');
Route::put('/obramodifica/{id}', 'App\Http\Controllers\ObraController@modifica');



Route::get('/obraexcel', 'App\Http\Controllers\ObraController@excel');
Route::delete('/obra/{id}', 'App\Http\Controllers\ObraController@destroy');

Route::get('/obra/{id}', 'App\Http\Controllers\ObraController@show');






Route::get('/sueldo/calcular', [AsistenciaController::class, 'calcularSueldo']);


Route::get('/reporte/tardanzas', [AsistenciaController::class, 'reporteTardanzas']);
Route::get('/reporte/salidasanticipadas', [AsistenciaController::class, 'reporteSalidasAnticipadas']);
Route::get('/reporte/asistenciadiaria', [AsistenciaController::class, 'reporteAsistenciaDiaria']);
Route::get('/reporte/mensual', [AsistenciaController::class, 'reporteMensual']);


Route::get('/reporte/tardanzast', [AsistenciaController::class, 'reporteTardanzast']);
Route::get('/reporte/salidasanticipadast', [AsistenciaController::class, 'reporteSalidasAnticipadast']);
Route::get('/reporte/asistenciadiariat', [AsistenciaController::class, 'reporteAsistenciaDiariat']);
Route::get('/reporte/mensualt', [AsistenciaController::class, 'reporteMensualt']);


Route::post('/asistencia/registra', [AsistenciaController::class, 'registrar']);
Route::post('/asistencia/resumen', [AsistenciaController::class, 'resumen']);


Route::post('/asistencia/registrah', [AsistenciaController::class, 'registrar_huellero']);





Route::post('/stoken', [TokenAccesoController::class, 'solicitar']);
Route::post('/vtoken', [TokenAccesoController::class, 'validar']);
Route::get('/reporte-personal', [AsistenciaController::class, 'reportePersonal']);
Route::get('/reporte-personal-excel', [AsistenciaController::class, 'reportePersonal_excel']);




Route::get('/uit/{anio}', [UitController::class, 'getByYear']);


