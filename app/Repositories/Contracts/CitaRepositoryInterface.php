<?php

namespace App\Repositories\Contracts;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato de Repositorio: CitaRepositoryInterface
 * Capa de Acceso a Datos para Citas Médicas (RQNF-04)
 */
interface CitaRepositoryInterface
{
    /**
     * Listar citas con soporte para filtros por doctor, paciente, estado y rango de fechas.
     *
     * @param array $filters ['doctor_id' => int, 'paciente_id' => int, 'estado' => string, 'desde' => string, 'hasta' => string]
     * @return Collection<int, Cita>
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Obtener una cita médica por su ID con sus relaciones cargadas (paciente, doctor).
     */
    public function findById(int $id): ?Cita;

    /**
     * Crear y persistir una nueva cita médica.
     */
    public function create(array $data): Cita;

    /**
     * Actualizar los datos de una cita existente (fecha, horario, motivo).
     */
    public function update(int $id, array $data): ?Cita;

    /**
     * Actualizar únicamente el estado de una cita (confirmada, cancelada, atendida).
     */
    public function updateEstado(int $id, string $estado): ?Cita;

    /**
     * Buscar si existe una cita activa en conflicto de horario para el mismo doctor.
     * Algoritmo de solapamiento de intervalos en servidor:
     * inicio_nuevo < fin_existente AND fin_nuevo > inicio_existente.
     *
     * @param int $doctorId ID del doctor a validar
     * @param string $fecha Fecha en formato Y-m-d
     * @param string $horaInicio Hora de inicio en formato H:i o H:i:s
     * @param string $horaFin Hora de fin en formato H:i o H:i:s
     * @param int|null $excludeCitaId ID de cita a excluir (para reprogramaciones PUT)
     */
    public function findConflictingAppointment(
        int $doctorId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excludeCitaId = null
    ): ?Cita;
}
