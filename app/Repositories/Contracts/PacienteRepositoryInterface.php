<?php

namespace App\Repositories\Contracts;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato de Repositorio: PacienteRepositoryInterface
 * Capa de Acceso a Datos (RQNF-04)
 */
interface PacienteRepositoryInterface
{
    /**
     * Obtener todos los pacientes registrados.
     *
     * @return Collection<int, Paciente>
     */
    public function getAll(): Collection;

    /**
     * Buscar paciente por su identificador único.
     */
    public function findById(int $id): ?Paciente;
}
