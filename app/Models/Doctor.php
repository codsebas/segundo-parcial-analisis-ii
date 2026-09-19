<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Dominio: Doctor
 * Representa al profesional médico del sistema.
 */
class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctores';

    public $timestamps = false; // Manejado vía DB created_at

    protected $fillable = [
        'nombre',
        'especialidad',
        'telefono',
        'email',
    ];

    /**
     * Citas asignadas al doctor.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'doctor_id');
    }
}
