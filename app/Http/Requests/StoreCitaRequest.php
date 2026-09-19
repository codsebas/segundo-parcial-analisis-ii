<?php

namespace App\Http\Requests;

/**
 * Validación de entrada para creación de citas médicas (RQF-01, RQF-08, RQNF-03)
 */
class StoreCitaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctores,id'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora_inicio' => ['required', 'date_format:H:i:s,H:i'],
            'hora_fin' => ['required', 'date_format:H:i:s,H:i', 'after:hora_inicio'],
            'motivo' => ['required', 'string', 'min:3', 'max:1000'],
            'estado' => ['nullable', 'string', 'in:pendiente,confirmada,cancelada,atendida'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'El paciente es obligatorio.',
            'paciente_id.exists' => 'El paciente seleccionado no existe en el sistema.',
            'doctor_id.required' => 'El doctor es obligatorio.',
            'doctor_id.exists' => 'El doctor seleccionado no existe en el sistema.',
            'fecha.required' => 'La fecha de la cita es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:mm o HH:mm:ss.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:mm o HH:mm:ss.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'motivo.required' => 'El motivo de la cita médica es obligatorio.',
            'motivo.min' => 'El motivo debe tener al menos 3 caracteres.',
            'estado.in' => 'El estado especificado no es válido (permitidos: pendiente, confirmada, cancelada, atendida).',
        ];
    }
}
