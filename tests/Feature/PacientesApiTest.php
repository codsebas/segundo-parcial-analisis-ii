<?php

namespace Tests\Feature;

use App\Models\Paciente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Suite de Pruebas Automatizadas: Creación y Validación de Pacientes
 * Valida:
 * - RQF-09: Registro de nuevos pacientes en el sistema
 * - RQNF-03: Códigos HTTP estandarizados (201 Created, 400 Bad Request)
 * - RQNF-04: Arquitectura en 4 capas (FormRequest -> Controller -> Service -> Repository)
 */
class PacientesApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [TEST 1] Creación exitosa de paciente con todos los campos válidos retorna HTTP 201.
     */
    public function test_crear_paciente_exitoso_retorna_http_201_y_datos(): void
    {
        $payload = [
            'nombre' => 'María Fernanda López',
            'telefono' => '+502 4123-9876',
            'email' => 'mflopez_test_' . uniqid() . '@clinica.com',
            'fecha_nacimiento' => '1995-08-20',
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'codigo' => 201,
                     'mensaje' => 'Paciente registrado exitosamente.',
                     'data' => [
                         'nombre' => 'María Fernanda López',
                         'telefono' => '+502 4123-9876',
                         'fecha_nacimiento' => '1995-08-20',
                     ]
                 ]);

        $this->assertDatabaseHas('pacientes', [
            'nombre' => 'María Fernanda López',
            'telefono' => '+502 4123-9876',
        ]);
    }

    /**
     * [TEST 2] Creación exitosa de paciente solo con el campo obligatorio nombre.
     */
    public function test_crear_paciente_solo_con_nombre_requerido_exitoso(): void
    {
        $payload = [
            'nombre' => 'Carlos Roberto Estrada',
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.nombre', 'Carlos Roberto Estrada');

        $this->assertDatabaseHas('pacientes', [
            'nombre' => 'Carlos Roberto Estrada',
        ]);
    }

    /**
     * [TEST 3] Contención: Omisión de campo obligatorio nombre retorna HTTP 400.
     */
    public function test_contencion_nombre_requerido_retorna_http_400(): void
    {
        $payload = [
            'telefono' => '+502 5555-0000',
            'email' => 'sin_nombre@clinica.com',
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(400)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonPath('codigo', 400)
                 ->assertJsonStructure(['errores' => ['nombre']]);
    }

    /**
     * [TEST 4] Contención: Formato de correo electrónico inválido retorna HTTP 400.
     */
    public function test_contencion_email_invalido_retorna_http_400(): void
    {
        $payload = [
            'nombre' => 'Paciente Con Correo Invalido',
            'email' => 'correo-no-valido-sin-arroba',
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(400)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonPath('codigo', 400)
                 ->assertJsonStructure(['errores' => ['email']]);
    }

    /**
     * [TEST 5] Contención: Correo electrónico duplicado retorna HTTP 400.
     */
    public function test_contencion_email_duplicado_retorna_http_400(): void
    {
        $pacienteExistente = Paciente::first();

        $payload = [
            'nombre' => 'Otro Paciente',
            'email' => $pacienteExistente->email,
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(400)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonPath('codigo', 400)
                 ->assertJsonStructure(['errores' => ['email']]);
    }

    /**
     * [TEST 6] Contención: Fecha de nacimiento futura retorna HTTP 400.
     */
    public function test_contencion_fecha_nacimiento_futura_retorna_http_400(): void
    {
        $payload = [
            'nombre' => 'Paciente Viajero del Tiempo',
            'fecha_nacimiento' => '2099-01-01',
        ];

        $response = $this->postJson('/api/pacientes', $payload);

        $response->assertStatus(400)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonPath('codigo', 400)
                 ->assertJsonStructure(['errores' => ['fecha_nacimiento']]);
    }
}
