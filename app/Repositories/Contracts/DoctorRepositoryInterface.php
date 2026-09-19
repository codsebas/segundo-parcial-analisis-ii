<?php

namespace App\Repositories\Contracts;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato de Repositorio: DoctorRepositoryInterface
 * Capa de Acceso a Datos (RQNF-04)
 */
interface DoctorRepositoryInterface
{
    /**
     * Obtener todos los doctores registrados.
     *
     * @return Collection<int, Doctor>
     */
    public function getAll(): Collection;

    /**
     * Buscar doctor por su identificador único.
     */
    public function findById(int $id): ?Doctor;
}
