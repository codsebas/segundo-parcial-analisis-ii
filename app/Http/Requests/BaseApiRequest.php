<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Clase Base para Form Requests de la API
 * Garantiza que las violaciones de validación respondan formalmente con HTTP 400 (RQNF-03)
 */
abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'codigo' => 400,
            'mensaje' => 'Datos de entrada inválidos o incompletos.',
            'errores' => $validator->errors()
        ], 400));
    }
}
