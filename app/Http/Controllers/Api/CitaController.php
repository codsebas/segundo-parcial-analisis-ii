<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCitasRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaEstadoRequest;
use App\Http\Requests\UpdateCitaHorarioRequest;
use App\Http\Resources\CitaResource;
use App\Services\CitaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador REST: CitaController
 * Capa de Controladores HTTP para el CRUD de Citas Médicas
 * Códigos HTTP normalizados: 200, 201, 400, 404, 409 (RQF-01, RQF-04, RQF-05, RQF-06, RQF-07, RQNF-03)
 */
class CitaController extends Controller
{
    public function __construct(
        protected CitaService $citaService
    ) {}

    /**
     * GET /api/citas
     * Lista citas con filtros opcionales (doctor_id, paciente_id, estado, desde, hasta).
     * Usado directamente para pintar el FullCalendar (RQF-02, RQF-06).
     */
    public function index(FilterCitasRequest $request): AnonymousResourceCollection
    {
        $citas = $this->citaService->listarCitas($request->validated());
        return CitaResource::collection($citas);
    }

    /**
     * POST /api/citas
     * Crea una cita médica nueva.
     * Responde HTTP 201 en éxito, HTTP 409 si hay conflicto de horario (RQF-01, RQF-03, RQNF-03).
     */
    public function store(StoreCitaRequest $request): JsonResponse
    {
        $cita = $this->citaService->crearCita($request->validated());

        return (new CitaResource($cita))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/citas/{id}
     * Devuelve el detalle completo de una cita médica (RQF-07, RQF-09).
     * Responde HTTP 200 en éxito, HTTP 404 si no existe.
     */
    public function show(int $id): JsonResponse|CitaResource
    {
        $cita = $this->citaService->obtenerCita($id);
        if (!$cita) {
            return response()->json([
                'status' => 'error',
                'codigo' => 404,
                'mensaje' => "Cita médica con ID {$id} no encontrada.",
            ], 404);
        }

        return new CitaResource($cita);
    }

    /**
     * PUT /api/citas/{id}
     * Reprograma fecha y horario de una cita médica (RQF-04).
     * Usado por el evento drag & drop del FullCalendar.
     * Responde HTTP 200 en éxito, HTTP 409 si hay conflicto, HTTP 404 si no existe.
     */
    public function update(UpdateCitaHorarioRequest $request, int $id): JsonResponse
    {
        $cita = $this->citaService->reprogramarCita($id, $request->validated());
        if (!$cita) {
            return response()->json([
                'status' => 'error',
                'codigo' => 404,
                'mensaje' => "Cita médica con ID {$id} no encontrada para reprogramación.",
            ], 404);
        }

        return (new CitaResource($cita))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * PATCH /api/citas/{id}/estado
     * Cambia el estado de la cita (confirmar, cancelar, atender) (RQF-05).
     * Preserva el registro histórico en base de datos.
     * Responde HTTP 200 en éxito, HTTP 404 si no existe, HTTP 400 si el estado es inválido.
     */
    public function updateEstado(UpdateCitaEstadoRequest $request, int $id): JsonResponse
    {
        $cita = $this->citaService->cambiarEstado($id, $request->validated()['estado']);
        if (!$cita) {
            return response()->json([
                'status' => 'error',
                'codigo' => 404,
                'mensaje' => "Cita médica con ID {$id} no encontrada para actualizar estado.",
            ], 404);
        }

        return (new CitaResource($cita))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * POST /api/citas/validar-disponibilidad
     * Valida formalmente si un horario se encuentra disponible para un doctor.
     * Retorna HTTP 200 si está libre, HTTP 409 si hay conflicto (RQF-03, RQNF-07).
     */
    public function validarDisponibilidad(\Illuminate\Http\Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => 'required|integer|exists:doctores,id',
            'fecha' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required|date_format:H:i:s,H:i',
            'hora_fin' => 'required|date_format:H:i:s,H:i',
            'exclude_cita_id' => 'nullable|integer',
        ]);

        $resultado = $this->citaService->validarDisponibilidad(
            (int) $validated['doctor_id'],
            $validated['fecha'],
            $validated['hora_inicio'],
            $validated['hora_fin'],
            isset($validated['exclude_cita_id']) ? (int) $validated['exclude_cita_id'] : null
        );

        $status = $resultado['disponible'] ? 200 : 409;
        return response()->json($resultado, $status);
    }
}
