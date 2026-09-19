<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador REST: PacienteController
 * Expone operaciones de lectura para pacientes (RQF-07, RQNF-03)
 */
class PacienteController extends Controller
{
    public function __construct(
        protected PacienteService $pacienteService
    ) {}

    /**
     * GET /api/pacientes
     * Listar todos los pacientes registrados en el sistema.
     */
    public function index(): AnonymousResourceCollection
    {
        $pacientes = $this->pacienteService->listarPacientes();
        return PacienteResource::collection($pacientes);
    }

    /**
     * GET /api/pacientes/{id}
     * Obtener el detalle de un paciente específico.
     */
    public function show(int $id): JsonResponse|PacienteResource
    {
        $paciente = $this->pacienteService->obtenerPaciente($id);
        if (!$paciente) {
            return response()->json([
                'status' => 'error',
                'codigo' => 404,
                'mensaje' => "Paciente con ID {$id} no encontrado.",
            ], 404);
        }

        return new PacienteResource($paciente);
    }
}
