<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Dominio: Paciente
 * Representa al paciente registrado para citas médicas.
 */
class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'fecha_nacimiento',
    ];

    /**
     * Citas registradas para el paciente.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }
}
