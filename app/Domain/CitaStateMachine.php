<?php

namespace App\Domain;

use App\Models\Cita;
use InvalidArgumentException;

/**
 * Dominio: CitaStateMachine
 * Controla el ciclo de vida y las transiciones formales de estado de una cita médica (RQF-05).
 *
 * Transiciones permitidas:
 * - pendiente  -> confirmada, cancelada
 * - confirmada -> atendida, cancelada
 * - cancelada  -> Estado terminal inmutable
 * - atendida   -> Estado terminal inmutable
 */
class CitaStateMachine
{
    /**
     * Grafo de transiciones de estado permitidas.
     */
    protected const TRANSICIONES_PERMITIDAS = [
        Cita::ESTADO_PENDIENTE => [
            Cita::ESTADO_CONFIRMADA,
            Cita::ESTADO_CANCELADA,
        ],
        Cita::ESTADO_CONFIRMADA => [
            Cita::ESTADO_ATENDIDA,
            Cita::ESTADO_CANCELADA,
        ],
        Cita::ESTADO_ATENDIDA => [], // Estado terminal
        Cita::ESTADO_CANCELADA => [], // Estado terminal
    ];

    /**
     * Valida si la transición de un estado a otro es válida en el dominio.
     *
     * @param string $estadoActual Estado actual de la cita
     * @param string $nuevoEstado Nuevo estado propuesto
     * @return bool
     */
    public static function puedeTransicionar(string $estadoActual, string $nuevoEstado): bool
    {
        if ($estadoActual === $nuevoEstado) {
            return true; // Idempotente
        }

        $destinosPermitidos = self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];
        return in_array($nuevoEstado, $destinosPermitidos, true);
    }

    /**
     * Ejecuta y valida la transición, arrojando excepción de dominio si no es válida.
     *
     * @throws InvalidArgumentException
     */
    public static function validarTransicion(string $estadoActual, string $nuevoEstado): void
    {
        if (!in_array($nuevoEstado, Cita::ESTADOS_VALIDOS, true)) {
            throw new InvalidArgumentException(
                "El estado '{$nuevoEstado}' no es reconocido. Estados permitidos: " . implode(', ', Cita::ESTADOS_VALIDOS)
            );
        }

        if (!self::puedeTransicionar($estadoActual, $nuevoEstado)) {
            throw new InvalidArgumentException(
                "Transición de estado no permitida: Una cita en estado '{$estadoActual}' no puede pasar a '{$nuevoEstado}'."
            );
        }
    }
}
