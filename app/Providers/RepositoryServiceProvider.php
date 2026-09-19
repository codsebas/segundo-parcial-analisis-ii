<?php

namespace App\Providers;

use App\Repositories\Contracts\CitaRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use App\Repositories\Eloquent\CitaRepository;
use App\Repositories\Eloquent\DoctorRepository;
use App\Repositories\Eloquent\PacienteRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Proveedor de Servicios de Inyección de Dependencias
 * Enlaza los contratos de repositorios con sus implementaciones concretas.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DoctorRepositoryInterface::class, DoctorRepository::class);
        $this->app->bind(PacienteRepositoryInterface::class, PacienteRepository::class);
        $this->app->bind(CitaRepositoryInterface::class, CitaRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
