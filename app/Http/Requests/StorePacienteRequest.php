<?php

namespace App\Http\Requests;

/**
 * Form Request: StorePacienteRequest
 * Valida la creación de pacientes y responde con HTTP 400 en caso de violación (RQNF-03, RQF-09)
 */
class StorePacienteRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:120', 'unique:pacientes,email'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre completo del paciente es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 120 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder los 25 caracteres.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado con otro paciente.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
        ];
    }
}
