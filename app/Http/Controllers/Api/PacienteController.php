<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Resources\PacienteResource;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador REST: PacienteController
 * Expone operaciones de lectura y registro para pacientes (RQF-07, RQF-09, RQNF-03)
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

    /**
     * POST /api/pacientes
     * Registrar un nuevo paciente en el sistema (RQF-09).
     */
    public function store(StorePacienteRequest $request): JsonResponse
    {
        $paciente = $this->pacienteService->crearPaciente($request->validated());

        return response()->json([
            'status' => 'success',
            'codigo' => 201,
            'mensaje' => 'Paciente registrado exitosamente.',
            'data' => new PacienteResource($paciente),
        ], 201);
    }
}

