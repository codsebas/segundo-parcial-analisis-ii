<?php

namespace App\Domain;

/**
 * Dominio: AppointmentConflictValidator
 * Motor matemático de cálculo de solapamiento de intervalos horarios continuos (RQF-03, RQNF-07).
 */
class AppointmentConflictValidator
{
    /**
     * Determina si dos intervalos horarios se solapan.
     * Condición matemática: [inicioA, finA) se interseca con [inicioB, finB)
     * sii (inicioA < finB) Y (finA > inicioB).
     *
     * Nota: Citas contiguas (ej. 08:00-09:00 y 09:00-10:00) NO se solapan.
     */
    public static function haySolapamiento(
        string $inicioA,
        string $finA,
        string $inicioB,
        string $finB
    ): bool {
        // Normalización a formato HH:mm:ss
        $inicioA = self::normalizarHora($inicioA);
        $finA    = self::normalizarHora($finA);
        $inicioB = self::normalizarHora($inicioB);
        $finB    = self::normalizarHora($finB);

        return ($inicioA < $finB) && ($finA > $inicioB);
    }

    /**
     * Clasifica el tipo de solapamiento detectado para diagnóstico forense.
     *
     * @return string Tipo de solapamiento (EXACTO, PARCIAL_IZQUIERDA, PARCIAL_DERECHA, ENVOLVENTE, CONTENIDO)
     */
    public static function clasificarSolapamiento(
        string $nuevoInicio,
        string $nuevoFin,
        string $existenteInicio,
        string $existenteFin
    ): ?string {
        if (!self::haySolapamiento($nuevoInicio, $nuevoFin, $existenteInicio, $existenteFin)) {
            return null;
        }

        $nI = self::normalizarHora($nuevoInicio);
        $nF = self::normalizarHora($nuevoFin);
        $eI = self::normalizarHora($existenteInicio);
        $eF = self::normalizarHora($existenteFin);

        if ($nI === $eI && $nF === $eF) {
            return 'SOLAPAMIENTO_EXACTO';
        }

        if ($nI < $eI && $nF > $eF) {
            return 'SOLAPAMIENTO_ENVOLVENTE';
        }

        if ($nI >= $eI && $nF <= $eF) {
            return 'SOLAPAMIENTO_CONTENIDO';
        }

        if ($nI < $eI && $nF > $eI) {
            return 'SOLAPAMIENTO_PARCIAL_INICIAL';
        }

        return 'SOLAPAMIENTO_PARCIAL_FINAL';
    }

    private static function normalizarHora(string $hora): string
    {
        $hora = trim($hora);
        if (strlen($hora) === 5) {
            return $hora . ':00';
        }
        return $hora;
    }
}
