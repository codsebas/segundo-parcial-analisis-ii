<?php

namespace App\Http\Requests;

/**
 * Validación de parámetros de consulta para filtrado de citas (RQF-06, RQNF-03)
 */
class FilterCitasRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'doctor_id' => ['nullable', 'integer'],
            'paciente_id' => ['nullable', 'integer'],
            'estado' => ['nullable', 'string', 'in:pendiente,confirmada,cancelada,atendida'],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
