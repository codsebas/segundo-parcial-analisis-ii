<?php

use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PacienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Sistema de Gestión de Citas Médicas
|--------------------------------------------------------------------------
| Rutas REST del sistema organizadas según la especificación del backlog
| y los criterios técnicos del examen (RQF-01 a RQF-08, RQNF-03, RQNF-04).
*/

// Endpoints de lectura para Doctores (RQF-07)
Route::get('/doctores', [DoctorController::class, 'index'])->name('api.doctores.index');
Route::get('/doctores/{id}', [DoctorController::class, 'show'])->name('api.doctores.show');

// Endpoints de lectura para Pacientes (RQF-07)
Route::get('/pacientes', [PacienteController::class, 'index'])->name('api.pacientes.index');
Route::get('/pacientes/{id}', [PacienteController::class, 'show'])->name('api.pacientes.show');

// Endpoints CRUD para Citas Médicas (RQF-01, RQF-04, RQF-05, RQF-06, RQF-07)
Route::get('/citas', [CitaController::class, 'index'])->name('api.citas.index');
Route::post('/citas', [CitaController::class, 'store'])->name('api.citas.store');
Route::get('/citas/{id}', [CitaController::class, 'show'])->name('api.citas.show');
Route::put('/citas/{id}', [CitaController::class, 'update'])->name('api.citas.update');
Route::patch('/citas/{id}/estado', [CitaController::class, 'updateEstado'])->name('api.citas.updateEstado');
