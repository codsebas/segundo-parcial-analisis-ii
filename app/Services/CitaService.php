<?php

namespace App\Services;

use App\Domain\AppointmentConflictValidator;
use App\Domain\CitaStateMachine;
use App\Exceptions\HorarioConflictException;
use App\Models\Cita;
use App\Repositories\Contracts\CitaRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

/**
 * Capa de Lógica de Negocio: CitaService
 * Reglas de validación de negocio, prevención de solapamientos (RQF-03, RQNF-07),
 * y gestión de ciclo de vida de estados de citas médicas (RQF-05).
 */
class CitaService
{
    public function __construct(
        protected CitaRepositoryInterface $citaRepository,
        protected DoctorRepositoryInterface $doctorRepository,
        protected PacienteRepositoryInterface $pacienteRepository
    ) {}

    /**
     * Listar citas con filtros aplicables (doctor, paciente, fechas, estado).
     */
    public function listarCitas(array $filters = []): Collection
    {
        return $this->citaRepository->getAll($filters);
    }

    /**
     * Obtener el detalle de una cita específica.
     */
    public function obtenerCita(int $id): ?Cita
    {
        return $this->citaRepository->findById($id);
    }

    /**
     * Valida si un horario está disponible para un doctor en una fecha determinada.
     * Útil para verificaciones previas o feedback en tiempo real.
     */
    public function validarDisponibilidad(
        int $doctorId,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $excludeCitaId = null
    ): array {
        if (strcmp($horaInicio, $horaFin) >= 0) {
            return [
                'disponible' => false,
                'motivo' => 'La hora de inicio debe ser anterior a la hora de fin.'
            ];
        }

        $conflicto = $this->citaRepository->findConflictingAppointment(
            $doctorId,
            $fecha,
            $horaInicio,
            $horaFin,
            $excludeCitaId
        );

        if ($conflicto) {
            $tipo = AppointmentConflictValidator::clasificarSolapamiento(
                $horaInicio,
                $horaFin,
                $conflicto->hora_inicio,
                $conflicto->hora_fin
            );

            return [
                'disponible' => false,
                'tipo_conflicto' => $tipo,
                'conflicto' => [
                    'id' => $conflicto->id,
                    'doctor_id' => $conflicto->doctor_id,
                    'fecha' => is_string($conflicto->fecha) ? $conflicto->fecha : $conflicto->fecha->format('Y-m-d'),
                    'hora_inicio' => $conflicto->hora_inicio,
                    'hora_fin' => $conflicto->hora_fin,
                    'estado' => $conflicto->estado,
                ]
            ];
        }

        return ['disponible' => true];
    }

    /**
     * Crear una nueva cita médica aplicando validación estricta de conflictos en servidor.
     *
     * @throws HorarioConflictException Si el doctor ya cuenta con una cita en ese horario (409)
     * @throws InvalidArgumentException Si los datos de hora son inconsistentes (400)
     */
    public function crearCita(array $data): Cita
    {
        // 1. Validación de existencia de doctor y paciente
        $doctor = $this->doctorRepository->findById((int) $data['doctor_id']);
        if (!$doctor) {
            throw new InvalidArgumentException("El doctor especificado no existe en el sistema.");
        }

        $paciente = $this->pacienteRepository->findById((int) $data['paciente_id']);
        if (!$paciente) {
            throw new InvalidArgumentException("El paciente especificado no existe en el sistema.");
        }

        // 2. Validación de coherencia horaria (hora_inicio < hora_fin)
        if (strcmp($data['hora_inicio'], $data['hora_fin']) >= 0) {
            throw new InvalidArgumentException("La hora de inicio debe ser anterior a la hora de fin.");
        }

        // 3. Validación de doble reserva en servidor (RQF-03, RQNF-07)
        $conflicto = $this->citaRepository->findConflictingAppointment(
            (int) $data['doctor_id'],
            $data['fecha'],
            $data['hora_inicio'],
            $data['hora_fin']
        );

        if ($conflicto) {
            $tipoSolapamiento = AppointmentConflictValidator::clasificarSolapamiento(
                $data['hora_inicio'],
                $data['hora_fin'],
                $conflicto->hora_inicio,
                $conflicto->hora_fin
            );

            throw new HorarioConflictException(
                "Conflicto de horario ({$tipoSolapamiento}): El {$doctor->nombre} ya tiene una cita activa ({$conflicto->hora_inicio} - {$conflicto->hora_fin}) en el intervalo solicitado.",
                $conflicto
            );
        }

        // 4. Asignación de estado por defecto
        if (empty($data['estado'])) {
            $data['estado'] = Cita::ESTADO_PENDIENTE;
        }

        return $this->citaRepository->create($data);
    }

    /**
     * Reprogramar fecha y horario de una cita existente (Usado por Drag & Drop de FullCalendar).
     *
     * @throws HorarioConflictException Si el nuevo intervalo colisiona con otra cita del doctor (409)
     * @throws InvalidArgumentException Si el rango de horas es inválido (400)
     */
    public function reprogramarCita(int $id, array $data): ?Cita
    {
        $cita = $this->citaRepository->findById($id);
        if (!$cita) {
            return null;
        }

        // Si la cita ya está cancelada o atendida, no se permite reprogramar
        if (in_array($cita->estado, [Cita::ESTADO_CANCELADA, Cita::ESTADO_ATENDIDA], true)) {
            throw new InvalidArgumentException(
                "No es posible reprogramar una cita que se encuentra en estado '{$cita->estado}'."
            );
        }

        $nuevaFecha = $data['fecha'] ?? (is_string($cita->fecha) ? $cita->fecha : $cita->fecha->format('Y-m-d'));
        $nuevaInicio = $data['hora_inicio'] ?? $cita->hora_inicio;
        $nuevaFin = $data['hora_fin'] ?? $cita->hora_fin;

        if (strcmp($nuevaInicio, $nuevaFin) >= 0) {
            throw new InvalidArgumentException("La hora de inicio debe ser anterior a la hora de fin.");
        }

        // Validación de solapamiento excluyendo la cita actual
        $conflicto = $this->citaRepository->findConflictingAppointment(
            $cita->doctor_id,
            $nuevaFecha,
            $nuevaInicio,
            $nuevaFin,
            $id
        );

        if ($conflicto) {
            $tipo = AppointmentConflictValidator::clasificarSolapamiento(
                $nuevaInicio,
                $nuevaFin,
                $conflicto->hora_inicio,
                $conflicto->hora_fin
            );

            throw new HorarioConflictException(
                "Conflicto de horario al reprogramar ({$tipo}): El doctor ya tiene una cita activa asignada en ese intervalo.",
                $conflicto
            );
        }

        $updateData = [
            'fecha' => $nuevaFecha,
            'hora_inicio' => $nuevaInicio,
            'hora_fin' => $nuevaFin,
        ];

        if (isset($data['motivo'])) {
            $updateData['motivo'] = $data['motivo'];
        }

        return $this->citaRepository->update($id, $updateData);
    }

    /**
     * Cambiar el estado de una cita médica utilizando la máquina de estados formal (RQF-05).
     * La cancelación preserva el registro histórico en base de datos.
     *
     * @throws InvalidArgumentException Si la transición de estado no es válida (400)
     */
    public function cambiarEstado(int $id, string $nuevoEstado): ?Cita
    {
        $cita = $this->citaRepository->findById($id);
        if (!$cita) {
            return null;
        }

        // Validación formal contra la máquina de estados del dominio
        CitaStateMachine::validarTransicion($cita->estado, $nuevoEstado);

        return $this->citaRepository->updateEstado($id, $nuevoEstado);
    }
}
