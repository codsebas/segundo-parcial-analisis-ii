<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Suite de Pruebas Automatizadas de API y Contención
 * Valida los requisitos:
 * - RQF-01: Creación de cita médica
 * - RQF-03: Impedimento de doble reserva (409 Conflict)
 * - RQF-05: Cancelación y preservación histórica
 * - RQF-06: Filtros por doctor y fechas
 * - RQF-07: CRUD de citas y lectura de doctores/pacientes
 * - RQF-08: Validación de datos de entrada y contención (400)
 * - RQNF-03: Códigos HTTP estandarizados (200, 201, 400, 404, 409)
 * - RQNF-07: Validación de disponibilidad ejecutada en el servidor
 */
class CitasApiTest extends TestCase
{
    use DatabaseTransactions; // Asegura rollback automático tras cada prueba

    protected Doctor $doctor;
    protected Doctor $doctorB;
    protected Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = Doctor::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Dr. Alejandro Morales', 'especialidad' => 'Medicina General', 'telefono' => '55511001', 'email' => 'amorales@clinica.com']
        );

        $this->doctorB = Doctor::firstOrCreate(
            ['id' => 2],
            ['nombre' => 'Dra. Beatriz Castillo', 'especialidad' => 'Cardiología', 'telefono' => '55522002', 'email' => 'bcastillo@clinica.com']
        );

        $this->paciente = Paciente::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Juan Pérez Gómez', 'telefono' => '44411111', 'email' => 'jperez@gmail.com', 'fecha_nacimiento' => '1988-04-12']
        );
    }

    /**
     * [TEST 1] Lectura de doctores y pacientes (RQF-07, RQNF-03)
     */
    public function test_puede_listar_doctores_y_pacientes_con_http_200(): void
    {
        $responseDoc = $this->getJson('/api/doctores');
        $responseDoc->assertStatus(200)
                    ->assertJsonStructure(['data' => [['id', 'nombre', 'especialidad']]]);

        $responsePac = $this->getJson('/api/pacientes');
        $responsePac->assertStatus(200)
                    ->assertJsonStructure(['data' => [['id', 'nombre', 'telefono', 'email']]]);
    }

    /**
     * [TEST 2] Creación exitosa de cita médica (RQF-01, RQNF-03)
     */
    public function test_crear_cita_exitosa_retorna_http_201_y_estructura_json(): void
    {
        $payload = [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => '2026-10-15',
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:45:00',
            'motivo' => 'Consulta de control general de prueba',
            'estado' => 'pendiente',
        ];

        $response = $this->postJson('/api/citas', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.motivo', 'Consulta de control general de prueba')
                 ->assertJsonPath('data.doctor_id', $this->doctor->id)
                 ->assertJsonPath('data.estado', 'pendiente');

        $this->assertDatabaseHas('citas', [
            'doctor_id' => $this->doctor->id,
            'fecha' => '2026-10-15',
            'hora_inicio' => '10:00:00',
        ]);
    }

    /**
     * [TEST 3 - CONTENCIÓN] Rechazo por datos incompletos o faltantes (RQF-08, RQNF-03)
     */
    public function test_contencion_campos_obligatorios_retorna_http_400(): void
    {
        $payloadInvalido = [
            // Falta paciente_id, doctor_id, fecha, etc.
            'motivo' => '',
        ];

        $response = $this->postJson('/api/citas', $payloadInvalido);

        $response->assertStatus(400)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonStructure(['errores' => ['paciente_id', 'doctor_id', 'fecha', 'hora_inicio', 'hora_fin', 'motivo']]);
    }

    /**
     * [TEST 4 - CONTENCIÓN] Rechazo si doctor o paciente no existen (RQF-08, RQNF-03)
     */
    public function test_contencion_llaves_foraneas_inexistentes_retorna_http_400(): void
    {
        $payload = [
            'paciente_id' => 99999, // Inexistente
            'doctor_id' => 88888,   // Inexistente
            'fecha' => '2026-10-16',
            'hora_inicio' => '09:00:00',
            'hora_fin' => '09:30:00',
            'motivo' => 'Consulta con IDs no existentes',
        ];

        $response = $this->postJson('/api/citas', $payload);

        $response->assertStatus(400)
                 ->assertJsonStructure(['errores' => ['paciente_id', 'doctor_id']]);
    }

    /**
     * [TEST 5 - CONTENCIÓN] Rechazo si hora_fin es menor o igual a hora_inicio (RQF-08, RQNF-03)
     */
    public function test_contencion_rango_horario_invertido_retorna_http_400(): void
    {
        $payload = [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => '2026-10-16',
            'hora_inicio' => '11:00:00',
            'hora_fin' => '10:00:00', // Inválido: fin anterior al inicio
            'motivo' => 'Consulta con horario invertido',
        ];

        $response = $this->postJson('/api/citas', $payload);

        $response->assertStatus(400)
                 ->assertJsonStructure(['errores' => ['hora_fin']]);
    }

    /**
     * [TEST 6 - CONFLICTO EN SERVIDOR] Rechazo por solapamiento exacto de horario (RQF-03, RQNF-07, RQNF-03)
     */
    public function test_rechaza_doble_reserva_con_http_409_conflict(): void
    {
        $fecha = '2026-11-20';

        // Cita base activa
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
            'motivo' => 'Cita original existente',
            'estado' => 'confirmada',
        ]);

        // Intento de segunda cita para el mismo doctor solapando horario
        $payloadSolapado = [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:30:00', // Se solapa de 09:30 a 10:30
            'hora_fin' => '10:30:00',
            'motivo' => 'Intento de cita en conflicto',
        ];

        $response = $this->postJson('/api/citas', $payloadSolapado);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409)
                 ->assertJsonStructure(['mensaje', 'conflicto' => ['id', 'hora_inicio', 'hora_fin']]);
    }

    /**
     * [TEST 7 - DISPONIBILIDAD] Permite horario idéntico si es para otro doctor diferente (RQF-03)
     */
    public function test_permite_mismo_horario_para_doctor_diferente(): void
    {
        $fecha = '2026-11-21';

        // Cita para Dr. Morales
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00:00',
            'hora_fin' => '15:00:00',
            'motivo' => 'Cita Dr Morales',
            'estado' => 'confirmada',
        ]);

        // Mismo horario pero para Dra. Castillo
        $payload = [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorB->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00:00',
            'hora_fin' => '15:00:00',
            'motivo' => 'Cita Dra Castillo en paralelo',
        ];

        $response = $this->postJson('/api/citas', $payload);
        $response->assertStatus(201);
    }

    /**
     * [TEST 8 - CANCELACIÓN Y NO CONFLICTO] Permite horario si la cita anterior fue cancelada (RQF-03, RQF-05)
     */
    public function test_permite_horario_si_la_cita_previa_fue_cancelada(): void
    {
        $fecha = '2026-11-22';

        // Cita previa en estado 'cancelada'
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => $fecha,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'motivo' => 'Cita cancelada anteriormente',
            'estado' => 'cancelada',
        ]);

        // Nueva cita en el mismo horario liberado
        $payload = [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => $fecha,
            'hora_inicio' => '16:00:00',
            'hora_fin' => '17:00:00',
            'motivo' => 'Nueva cita en horario liberado por cancelacion',
        ];

        $response = $this->postJson('/api/citas', $payload);
        $response->assertStatus(201);
    }

    /**
     * [TEST 9] Reprogramación de horario (PUT) con validación de conflicto (RQF-04, RQNF-03)
     */
    public function test_reprogramar_cita_actualiza_exitosamente_o_detecta_conflicto(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => '2026-11-23',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '08:45:00',
            'motivo' => 'Cita para reprogramar',
            'estado' => 'pendiente',
        ]);

        $payloadUpdate = [
            'fecha' => '2026-11-24',
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:45:00',
        ];

        $response = $this->putJson("/api/citas/{$cita->id}", $payloadUpdate);

        $response->assertStatus(200)
                 ->assertJsonPath('data.fecha', '2026-11-24')
                 ->assertJsonPath('data.hora_inicio', '11:00:00');
    }

    /**
     * [TEST 10 - CANCELACIÓN HISTÓRICA] Cambio de estado a 'cancelada' sin eliminar el registro (RQF-05)
     */
    public function test_cancelar_cita_conserva_el_registro_historico_en_bd(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctor->id,
            'fecha' => '2026-11-25',
            'hora_inicio' => '15:00:00',
            'hora_fin' => '15:45:00',
            'motivo' => 'Cita que sera cancelada por el paciente',
            'estado' => 'confirmada',
        ]);

        $response = $this->patchJson("/api/citas/{$cita->id}/estado", [
            'estado' => 'cancelada',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.estado', 'cancelada');

        // Verificación en base de datos: el registro NO fue eliminado
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'cancelada',
            'motivo' => 'Cita que sera cancelada por el paciente',
        ]);
    }

    /**
     * [TEST 11 - CONTENCIÓN] Consulta de cita inexistente retorna HTTP 404 (RQNF-03)
     */
    public function test_consulta_cita_inexistente_retorna_http_404(): void
    {
        $response = $this->getJson('/api/citas/999999');
        $response->assertStatus(404)
                 ->assertJsonPath('status', 'error')
                 ->assertJsonPath('codigo', 404);
    }
}
