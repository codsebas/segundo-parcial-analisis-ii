<?php

namespace App\Repositories\Eloquent;

use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementación Eloquent de DoctorRepositoryInterface
 */
class DoctorRepository implements DoctorRepositoryInterface
{
    public function getAll(): Collection
    {
        return Doctor::orderBy('nombre', 'asc')->get();
    }

    public function findById(int $id): ?Doctor
    {
        return Doctor::find($id);
    }
}
