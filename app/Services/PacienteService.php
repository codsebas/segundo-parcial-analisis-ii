<?php

namespace App\Services;

use App\Models\Paciente;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Capa de Negocio: PacienteService
 * Orquesta la lógica y consultas para los pacientes.
 */
class PacienteService
{
    public function __construct(
        protected PacienteRepositoryInterface $pacienteRepository
    ) {}

    /**
     * Listar todos los pacientes registrados.
     *
     * @return Collection<int, Paciente>
     */
    public function listarPacientes(): Collection
    {
        return $this->pacienteRepository->getAll();
    }

    /**
     * Obtener el detalle de un paciente específico.
     */
    public function obtenerPaciente(int $id): ?Paciente
    {
        return $this->pacienteRepository->findById($id);
    }
}
