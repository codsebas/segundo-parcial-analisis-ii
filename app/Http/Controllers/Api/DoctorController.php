<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controlador REST: DoctorController
 * Expone operaciones de lectura para doctores (RQF-07, RQNF-03)
 */
class DoctorController extends Controller
{
    public function __construct(
        protected DoctorService $doctorService
    ) {}

    /**
     * GET /api/doctores
     * Listar todos los doctores disponibles en el sistema.
     */
    public function index(): AnonymousResourceCollection
    {
        $doctores = $this->doctorService->listarDoctores();
        return DoctorResource::collection($doctores);
    }

    /**
     * GET /api/doctores/{id}
     * Obtener el detalle de un doctor específico.
     */
    public function show(int $id): JsonResponse|DoctorResource
    {
        $doctor = $this->doctorService->obtenerDoctor($id);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'codigo' => 404,
                'mensaje' => "Doctor con ID {$id} no encontrado.",
            ], 404);
        }

        return new DoctorResource($doctor);
    }
}
