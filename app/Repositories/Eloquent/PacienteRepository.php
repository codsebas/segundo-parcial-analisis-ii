<?php

namespace App\Repositories\Eloquent;

use App\Models\Paciente;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementación Eloquent de PacienteRepositoryInterface
 */
class PacienteRepository implements PacienteRepositoryInterface
{
    public function getAll(): Collection
    {
        return Paciente::orderBy('nombre', 'asc')->get();
    }

    public function findById(int $id): ?Paciente
    {
        return Paciente::find($id);
    }
}
