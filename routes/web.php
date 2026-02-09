<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\ElementosRtoController;
use App\Http\Controllers\Observaciones;
use App\Http\Controllers\Reclamos;
use App\Http\Controllers\RtoController;
use App\Http\Controllers\Usuarios;
use App\Http\Controllers\Proveedores;
use App\Http\Controllers\Informes;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\RtoDetalleController;
use App\Http\Controllers\Perfiles;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\LogisticaController;

// Crear usuario admin (Solo usar una vez)
// Route::get('/crear-admin',[AuthController::class, 'crearAdmin'])->name('crear-admin');

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/loguear', [AuthController::class, 'loguear'])->name('loguear');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [Dashboard::class, 'index'])->name('home');
    Route::get('/mi-perfil', [Usuarios::class, 'miPerfil'])->name('mi-perfil');
    Route::put('/mi-perfil/actualizar', [Usuarios::class, 'actualizarPerfil'])->name('mi-perfil.actualizar');
});



Route::prefix('remitos')->middleware(['auth', 'Checkrol:remitos'])->group(function () {
    Route::get('/', [RtoController::class, 'index'])->name('remitos');
    Route::post('/store', [RtoController::class, 'store'])->name('remitos.store');
    Route::get('/edit/{id}', [RtoController::class, 'edit'])->name('remitos.edit');
    Route::post('/storeElementoRto', [ElementosRtoController::class, 'storeElementoRto'])->name('storeElementoRto');
    Route::post('/storeRtoDetalle', [RtoDetalleController::class, 'store'])->name('storeRtoDetalle.store');
    Route::post('actualizarCampo', [RtoDetalleController::class, 'actualizarCampo'])->name('actualizarCampo');
    Route::get('obtenerValor/{id}/{field}', [RtoDetalleController::class, 'obtenerValor'])->name('obtenerValor');
    Route::post('/deleteRtoDetalle/{id}', [RtoDetalleController::class, 'delete'])->name('deleteRtoDetalle');
    Route::get('pendientes', [RtoController::class, 'pendientes'])->name('remitos.pendientes');
    Route::post('/actualizarEstado/{id}', [RtoController::class, 'actualizarEstado'])->name('actualizarEstado');
    Route::post('/remitos/actualizar/{id}', [RtoController::class, 'actualizar'])->name('actualizarRemito');
});

Route::prefix('reclamos')->middleware(['auth', 'Checkrol:reclamos'])->group(function () {
    Route::get('/', [Reclamos::class, 'index'])->name('reclamos');
    Route::post('/reclamos', [Reclamos::class, 'store'])->name('reclamos.store');
    Route::put('/reclamos/{id}', [Reclamos::class, 'update'])->name('reclamos.update');
    Route::get('/show/{rtoId?}', [Reclamos::class, 'show'])->name('reclamos.show');
    Route::delete('/destroy/{id}', [Reclamos::class, 'destroy'])->name('reclamos.destroy');
});

Route::prefix('observaciones')->middleware(['auth', 'Checkrol:observaciones'])->group(function () {
    Route::get('/', [Observaciones::class, 'index'])->name('observaciones');
    Route::post('/store', [Observaciones::class, 'store'])->name('observaciones.store');
    Route::get('/show/{remito}', [Observaciones::class, 'show'])->name('observaciones.show');
    Route::put('/update/{id}', [Observaciones::class, 'update'])->name('observaciones.update');
    Route::post('/destroy/{id}', [Observaciones::class, 'destroy'])->name('observaciones.destroy');
});

Route::prefix('usuarios')->middleware(['auth', 'Checkrol:usuarios'])->group(function () {
    Route::get('/', [Usuarios::class, 'index'])->name('usuarios');
    Route::get('/create', [Usuarios::class, 'create'])->name('usuarios.create');
    Route::post('/store', [Usuarios::class, 'store'])->name('usuarios.store');
    Route::get('/edit/{id}', [Usuarios::class, 'edit'])->name('usuarios.edit');
    Route::put('/update/{id}', [Usuarios::class, 'update'])->name('usuarios.update');
    Route::post('/estado/{id}', [Usuarios::class, 'estado'])->name('usuarios.estado');
    Route::delete('/destroy/{id}', [Usuarios::class, 'destroy'])->name('usuarios.destroy');
});

Route::prefix('proveedores')->middleware(['auth', 'Checkrol:proveedores'])->group(function () {
    Route::get('/', [Proveedores::class, 'index'])->name('proveedores');
    Route::get('/create', [Proveedores::class, 'create'])->name('proveedores.create');
    Route::post('/store', [Proveedores::class, 'store'])->name('proveedores.store');
    Route::get('/edit/{id}', [Proveedores::class, 'edit'])->name('proveedores.edit');
    Route::put('/update/{id}', [Proveedores::class, 'update'])->name('proveedores.update');
    Route::post('/estado/{id}', [Proveedores::class, 'estado'])->name('proveedores.estado');

    Route::get('/camiones', [Proveedores::class, 'indexCamiones'])->name('proveedores.camiones');
    Route::get('/camiones/create', [Proveedores::class, 'createCamiones'])->name('proveedores.camiones.create');
    Route::post('/camiones/store', [Proveedores::class, 'storeCamiones'])->name('proveedores.camiones.store');
    Route::get('/camiones/edit/{id}', [Proveedores::class, 'editCamiones'])->name('proveedores.camiones.edit');
    Route::put('/camiones/update/{id}', [Proveedores::class, 'updateCamiones'])->name('proveedores.camiones.update');

    Route::get('/{id}/camiones', [Proveedores::class, 'getCamiones']);

});

Route::prefix('informes')->middleware(['auth', 'Checkrol:informes'])->group(function () {
    Route::get('/', [Informes::class, 'index'])->name('informes');
});

Route::prefix('productos')->middleware(['auth', 'Checkrol:productos'])->group(function () {
    Route::get('/', [ProductosController::class, 'index'])->name('productos');
    Route::post('/cotizacion/guardar', [ProductosController::class, 'guardarCotizacion'])->name('cotizacion.guardar');
    Route::post('/cotizacion/actualizar-externa', [ProductosController::class, 'actualizarCotizacionExterna'])->name('cotizacion.actualizar-externa');
    Route::post('/productos', [ProductosController::class, 'store'])->name('productos.store');
    Route::get('/{id}/edit', [ProductosController::class, 'edit'])->name('productos.edit');
    Route::put('/update{id}', [ProductosController::class, 'update'])->name('productos.update');
    Route::delete('/delete{id}', [ProductosController::class, 'destroy'])->name('productos.destroy');
});

Route::prefix('perfiles')->middleware(['auth', 'Checkrol:perfiles'])->group(function () {
    Route::get('/', [Perfiles::class, 'index'])->name('perfiles');
    Route::get('/create', [Perfiles::class, 'create'])->name('perfiles.create');
    Route::post('/store', [Perfiles::class, 'store'])->name('perfiles.store');
    Route::get('/edit/{id}', [Perfiles::class, 'edit'])->name('perfiles.edit');
    Route::put('/update/{id}', [Perfiles::class, 'update'])->name('perfiles.update');
    Route::delete('/destroy/{id}', [Perfiles::class, 'destroy'])->name('perfiles.destroy');
});

Route::prefix('logistica')->middleware(['auth', 'Checkrol:logistica'])->group(function () {
    Route::get('/', [LogisticaController::class, 'index'])->name('logistica');
    Route::post('/store', [LogisticaController::class, 'store'])->name('logistica.store');
    Route::post('/actualizarCampo', [LogisticaController::class, 'actualizarCampo'])->name('logistica.actualizarCampo');
    Route::delete('/delete/{id}', [LogisticaController::class, 'destroy'])->name('logistica.destroy');
});

Route::prefix('configuracion')->middleware(['auth', 'Checkrol:configuracion'])->group(function () {
    Route::get('/', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::get('/numeracion', [ConfiguracionController::class, 'numeracion'])->name('configuracion.numeracion');
    Route::post('/reiniciar-numeracion', [ConfiguracionController::class, 'reiniciarNumeracion'])->name('configuracion.reiniciar-numeracion');
    Route::get('/audit-log', [ConfiguracionController::class, 'auditLog'])->name('configuracion.audit-log');
    Route::get('/audit-log/{id}', [ConfiguracionController::class, 'auditLogDetalle'])->name('configuracion.audit-log.detalle');
});
