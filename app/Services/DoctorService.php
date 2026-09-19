<?php

namespace App\Services;

use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Capa de Negocio: DoctorService
 * Orquesta la lógica y consultas para los doctores.
 */
class DoctorService
{
    public function __construct(
        protected DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Listar todos los doctores disponibles.
     *
     * @return Collection<int, Doctor>
     */
    public function listarDoctores(): Collection
    {
        return $this->doctorRepository->getAll();
    }

    /**
     * Obtener el detalle de un doctor específico.
     */
    public function obtenerDoctor(int $id): ?Doctor
    {
        return $this->doctorRepository->findById($id);
    }
}
