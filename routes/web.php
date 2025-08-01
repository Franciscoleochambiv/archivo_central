<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//Route::get('/enviar-oficina', [DocumentosController::class, 'eoficina'])->name('enviar-oficina');

Route::get('/buscar', function () {
    return Inertia::render('Buscar');
})->name('buscar');


Route::get('/addexpe', function () {
    return Inertia::render('Expe/Add');
})->name('addexpe');

Route::get('/editexpe/{id}', function ($id) {
    return Inertia::render('Expe/Edit', ['id' => $id]);
})->name('editexpe');






Route::get('/listado', function () {
    return Inertia::render('Listado');
})->name('listado');

Route::get('/editm/{id}', function ($id) {
    return Inertia::render('Editm', ['id' => $id]);
})->name('editm');

Route::get('/listadom', function () {
    return Inertia::render('Metas/Index');
})->name('listadom');

Route::get('/addmeta', function () {
    return Inertia::render('Metas/Add');
})->name('addmeta');

Route::get('/editmeta/{id}', function ($id) {
    return Inertia::render('Metas/Edit', ['id' => $id]);
})->name('editmeta');



Route::get('/listafp', function () {
    return Inertia::render('Afp/Index');
})->name('listafp');

Route::get('/addafp', function () {
    return Inertia::render('Afp/Add');
})->name('addafp');

Route::get('/editafp/{id}', function ($id) {
    return Inertia::render('Afp/Edit', ['id' => $id]);
})->name('editafp');




Route::get('/listonp', function () {
    return Inertia::render('Onp/Index');
})->name('listonp');

Route::get('/addonp', function () {
    return Inertia::render('Onp/Add');
})->name('addonp');

Route::get('/editonp/{id}', function ($id) {
    return Inertia::render('Onp/Edit', ['id' => $id]);
})->name('editonp');



Route::get('/listcargo', function () {
    return Inertia::render('Cargo/Index');
})->name('listcargo');

Route::get('/addcargo', function () {
    return Inertia::render('Cargo/Add');
})->name('addcargo');

Route::get('/editcargo/{id}', function ($id) {
    return Inertia::render('Cargo/Edit', ['id' => $id]);
})->name('editcargo');







Route::get('/listplanilla', function () {
    return Inertia::render('Planilla/Index');
})->name('listplanilla');

Route::get('/addplanilla', function () {
    return Inertia::render('Planilla/Add');
})->name('addplanilla');

Route::get('/editplanilla/{id}', function ($id) {
    return Inertia::render('Planilla/Edit', ['id' => $id]);
})->name('editplanilla');


Route::get('/repo', function () {
    return Inertia::render('Reportes/Index');
})->name('repo');


Route::get('/procesos', function () {
    return Inertia::render('Procesos/Index');
})->name('procesos');




Route::get('/listobras', function () {
    return Inertia::render('Expe/Index');
})->name('listobras');


Route::get('/addobra', function () {
    return Inertia::render('Obras/Add');
})->name('addobra');

Route::get('/editobra/{id}', function ($id) {
    return Inertia::render('Obras/Edit', ['id' => $id]);
})->name('editobra');







require __DIR__.'/auth.php';
