<?php

namespace App\Repositories\Eloquent;

use App\Models\Cita;
use App\Repositories\Contracts\CitaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementación Eloquent de CitaRepositoryInterface
 * Maneja todas las operaciones de persistencia y consultas especializadas de citas.
 */
class CitaRepository implements CitaRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        $query = Cita::with(['paciente', 'doctor']);

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['paciente_id'])) {
            $query->where('paciente_id', $filters['paciente_id']);
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['desde'])) {
            $query->whereDate('fecha', '>=', $filters['desde']);
        }

        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha', '<=', $filters['hasta']);
        }

        return $query->orderBy('fecha', 'asc')
                     ->orderBy('hora_inicio', 'asc')
                     ->get();
    }

    public function findById(int $id): ?Cita
    {
        return Cita::with(['paciente', 'doctor'])->find($id);
    }

    public function create(array $data): Cita
    {
        $cita = Cita::create($data);
        return $cita->load(['paciente', 'doctor']);
    }

    public function update(int $id, array $data): ?Cita
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return null;
        }

        $cita->update($data);
        return $cita->fresh(['paciente', 'doctor']);
    }

    public function updateEstado(int $id, string $estado): ?Cita
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return null;
        }

        $cita->estado = $estado;
        $cita->save();
        return $cita->fresh(['paciente', 'doctor']);
    }

    /**
     * Detección de solapamiento de horarios en el servidor (RQF-03, RQNF-07).
     * Solo citas activas ('pendiente', 'confirmada') generan colisión.
     * Citas canceladas o atendidas en el pasado no impiden agendar.
     */
    public function findConflictingAppointment(
        int $doctorId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excludeCitaId = null
    ): ?Cita {
        $query = Cita::with(['paciente', 'doctor'])
            ->where('doctor_id', $doctorId)
            ->whereDate('fecha', $fecha)
            ->activas()
            ->where(function ($q) use ($horaInicio, $horaFin) {
                $q->where('hora_inicio', '<', $horaFin)
                  ->where('hora_fin', '>', $horaInicio);
            });

        if ($excludeCitaId !== null) {
            $query->where('id', '!=', $excludeCitaId);
        }

        return $query->first();
    }
}
