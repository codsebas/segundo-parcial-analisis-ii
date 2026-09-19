<?php

namespace App\Http\Requests;

/**
 * Validación de entrada para cambio de estado de citas médicas (RQF-05, RQNF-03)
 */
class UpdateCitaEstadoRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', 'in:pendiente,confirmada,cancelada,atendida'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'Estado inválido. Los estados permitidos son: pendiente, confirmada, cancelada, atendida.',
        ];
    }
}
