<?php

namespace App\Exceptions;

use App\Models\Cita;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Excepción de Dominio: Conflicto de Horario (Solapamiento)
 * Se lanza cuando se intenta agendar o reprogramar una cita para un doctor
 * en un intervalo de tiempo que ya se encuentra ocupado por otra cita activa.
 * Retorna formalmente HTTP 409 Conflict (RQF-03, RQNF-03, RQNF-07).
 */
class HorarioConflictException extends Exception
{
    protected ?Cita $conflicto;

    public function __construct(
        string $message = "Conflicto de horario: El doctor ya cuenta con una cita activa en el intervalo seleccionado.",
        ?Cita $conflicto = null,
        int $code = 409,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->conflicto = $conflicto;
    }

    public function getConflicto(): ?Cita
    {
        return $this->conflicto;
    }

    /**
     * Renderizar la excepción como respuesta JSON estructurada con código HTTP 409.
     */
    public function render(Request $request): JsonResponse
    {
        $payload = [
            'status' => 'error',
            'codigo' => 409,
            'mensaje' => $this->getMessage(),
        ];

        if ($this->conflicto) {
            $payload['conflicto'] = [
                'id' => $this->conflicto->id,
                'doctor_id' => $this->conflicto->doctor_id,
                'doctor_nombre' => $this->conflicto->doctor?->nombre,
                'paciente_nombre' => $this->conflicto->paciente?->nombre,
                'fecha' => is_string($this->conflicto->fecha) ? $this->conflicto->fecha : $this->conflicto->fecha->format('Y-m-d'),
                'hora_inicio' => $this->conflicto->hora_inicio,
                'hora_fin' => $this->conflicto->hora_fin,
                'estado' => $this->conflicto->estado,
            ];
        }

        return response()->json($payload, 409);
    }
}
