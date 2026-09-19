<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Dominio: Cita
 * Representa la cita médica entre un paciente y un doctor.
 */
class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    public const ESTADO_PENDIENTE  = 'pendiente';
    public const ESTADO_CONFIRMADA = 'confirmada';
    public const ESTADO_CANCELADA  = 'cancelada';
    public const ESTADO_ATENDIDA   = 'atendida';

    public const ESTADOS_VALIDOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
        self::ESTADO_CANCELADA,
        self::ESTADO_ATENDIDA,
    ];

    public const ESTADOS_ACTIVOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_CONFIRMADA,
    ];

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'paciente_id' => 'integer',
        'doctor_id' => 'integer',
    ];

    /**
     * Relación con Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Relación con Doctor.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Scope para citas que generan ocupación de agenda (activas).
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereIn('estado', self::ESTADOS_ACTIVOS);
    }

    /**
     * Scope para filtrar por doctor.
     */
    public function scopePorDoctor(Builder $query, int $doctorId): Builder
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope para filtrar por rango de fechas.
     */
    public function scopeRangoFechas(Builder $query, ?string $desde, ?string $hasta): Builder
    {
        if ($desde) {
            $query->where('fecha', '>=', $desde);
        }
        if ($hasta) {
            $query->where('fecha', '<=', $hasta);
        }
        return $query;
    }
}
