<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    /**
     * Paleta de colores estándar para FullCalendar según el estado de la cita (RQF-10).
     */
    protected const COLORES_ESTADO = [
        'pendiente'  => ['bg' => '#fef3c7', 'border' => '#f59e0b', 'text' => '#92400e'],
        'confirmada' => ['bg' => '#e0f2fe', 'border' => '#2b6b8a', 'text' => '#0369a1'],
        'atendida'   => ['bg' => '#e6f8f0', 'border' => '#0db26b', 'text' => '#065f46'],
        'cancelada'  => ['bg' => '#fee2e2', 'border' => '#ef4444', 'text' => '#991b1b'],
    ];

    public function toArray(Request $request): array
    {
        $fechaStr = is_string($this->fecha) ? $this->fecha : $this->fecha->format('Y-m-d');
        $color = self::COLORES_ESTADO[$this->estado] ?? self::COLORES_ESTADO['pendiente'];

        return [
            'id' => $this->id,
            'paciente_id' => $this->paciente_id,
            'doctor_id' => $this->doctor_id,
            'fecha' => $fechaStr,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'paciente' => new PacienteResource($this->whenLoaded('paciente')),

            // Campos optimizados para renderizado directo en FullCalendar v6 (RQF-02, RQF-10)
            'title' => ($this->paciente ? $this->paciente->nombre : 'Paciente #' . $this->paciente_id) . ' - ' . ($this->doctor ? $this->doctor->nombre : 'Dr. #' . $this->doctor_id),
            'start' => "{$fechaStr}T{$this->hora_inicio}",
            'end'   => "{$fechaStr}T{$this->hora_fin}",
            'backgroundColor' => $color['bg'],
            'borderColor'     => $color['border'],
            'textColor'       => $color['text'],
            'extendedProps'   => [
                'doctor_nombre'       => $this->doctor?->nombre,
                'doctor_especialidad' => $this->doctor?->especialidad,
                'paciente_nombre'     => $this->paciente?->nombre,
                'paciente_telefono'   => $this->paciente?->telefono,
                'motivo'              => $this->motivo,
                'estado'              => $this->estado,
            ],
        ];
    }
}
