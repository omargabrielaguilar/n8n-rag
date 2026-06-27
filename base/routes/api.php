<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;

// Ruta de prueba para verificar que el usuario está autenticado
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de tu SaaS Médico
Route::middleware(['auth:sanctum'])->group(function () {
    // Listar citas
    Route::get('/appointments', [AppointmentController::class, 'index']);

    // Crear una cita
    Route::post('/appointments', [AppointmentController::class, 'store']);

    // Actualizar el estado de una cita (ej: de pending a confirmed)
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
});
