<?php

namespace App\Http\Requests;

/**
 * Validación de entrada para reprogramación de citas médicas (RQF-04, RQF-08, RQNF-03)
 */
class UpdateCitaHorarioRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora_inicio' => ['required', 'date_format:H:i:s,H:i'],
            'hora_fin' => ['required', 'date_format:H:i:s,H:i', 'after:hora_inicio'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener formato YYYY-MM-DD.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:mm o HH:mm:ss.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:mm o HH:mm:ss.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
