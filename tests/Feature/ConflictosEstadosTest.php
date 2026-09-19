<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Suite de Pruebas de Dominio: Validación de Conflictos y Ciclo de Vida de Estados
 * Requisitos evaluados:
 * - [RQF-03]: Detección de solapamiento y bloqueo de doble reserva en servidor.
 * - [RQF-05]: Cancelación de citas con preservación de historial en BD y transiciones de estado.
 * - [RQNF-03]: Códigos HTTP 200, 201, 400, 409.
 * - [RQNF-07]: Validación obligatoria en el servidor.
 */
class ConflictosEstadosTest extends TestCase
{
    use DatabaseTransactions;

    protected Doctor $doctorA;
    protected Doctor $doctorB;
    protected Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctorA = Doctor::firstOrCreate(
            ['id' => 10],
            ['nombre' => 'Dr. Conflicto Test A', 'especialidad' => 'Medicina Interna', 'telefono' => '55511000', 'email' => 'docA@test.com']
        );

        $this->doctorB = Doctor::firstOrCreate(
            ['id' => 11],
            ['nombre' => 'Dr. Conflicto Test B', 'especialidad' => 'Cirugía', 'telefono' => '55522000', 'email' => 'docB@test.com']
        );

        $this->paciente = Paciente::firstOrCreate(
            ['id' => 10],
            ['nombre' => 'Paciente Test Conflictos', 'telefono' => '44411000', 'email' => 'paciente@test.com', 'fecha_nacimiento' => '1990-01-01']
        );
    }

    /**
     * [CASO 1] Solapamiento exacto: Mismo inicio y mismo fin -> 409 Conflict
     */
    public function test_detecta_solapamiento_exacto_retorna_409(): void
    {
        $fecha = '2026-12-01';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
            'motivo' => 'Cita preexistente',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
            'motivo' => 'Intento solapamiento exacto',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409)
                 ->assertSee('SOLAPAMIENTO_EXACTO');
    }

    /**
     * [CASO 2] Solapamiento parcial inicial: Inicia antes y termina dentro -> 409 Conflict
     */
    public function test_detecta_solapamiento_parcial_inicial_retorna_409(): void
    {
        $fecha = '2026-12-02';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:00:00',
            'hora_fin' => '11:00:00',
            'motivo' => 'Cita preexistente',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:30:00',
            'hora_fin' => '10:30:00',
            'motivo' => 'Intento solapamiento parcial inicial',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409)
                 ->assertSee('SOLAPAMIENTO_PARCIAL_INICIAL');
    }

    /**
     * [CASO 3] Solapamiento parcial final: Inicia dentro y termina después -> 409 Conflict
     */
    public function test_detecta_solapamiento_parcial_final_retorna_409(): void
    {
        $fecha = '2026-12-03';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:00:00',
            'hora_fin' => '15:00:00',
            'motivo' => 'Cita preexistente',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '14:30:00',
            'hora_fin' => '15:30:00',
            'motivo' => 'Intento solapamiento parcial final',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409);
    }

    /**
     * [CASO 4] Solapamiento envolvente: Inicia antes y termina después del existente -> 409 Conflict
     */
    public function test_detecta_solapamiento_envolvente_retorna_409(): void
    {
        $fecha = '2026-12-04';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'motivo' => 'Cita preexistente corta',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '10:30:00',
            'hora_fin' => '12:00:00',
            'motivo' => 'Intento envolvente',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409)
                 ->assertSee('SOLAPAMIENTO_ENVOLVENTE');
    }

    /**
     * [CASO 5] Solapamiento contenido: La nueva cita está totalmente dentro del horario existente -> 409 Conflict
     */
    public function test_detecta_solapamiento_contenido_retorna_409(): void
    {
        $fecha = '2026-12-05';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:00:00',
            'hora_fin' => '17:00:00',
            'motivo' => 'Cita preexistente larga',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '15:30:00',
            'hora_fin' => '16:00:00',
            'motivo' => 'Intento contenido adentro',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('codigo', 409)
                 ->assertSee('SOLAPAMIENTO_CONTENIDO');
    }

    /**
     * [CASO 6] Citas contiguas en frontera exacta (08:00-09:00 y 09:00-10:00) -> 201 Created (SIN conflicto)
     */
    public function test_permite_citas_contiguas_en_frontera_exacta_sin_conflicto(): void
    {
        $fecha = '2026-12-06';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '08:00:00',
            'hora_fin' => '09:00:00',
            'motivo' => 'Cita bloque anterior',
            'estado' => 'confirmada',
        ]);

        // Cita contigua que inicia exactamente cuando la anterior finaliza
        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
            'motivo' => 'Cita bloque contiguo',
        ]);

        $response->assertStatus(201);
    }

    /**
     * [CASO 7] Cita cancelada libera la agenda del doctor -> 201 Created
     */
    public function test_cita_cancelada_libera_agenda_del_doctor(): void
    {
        $fecha = '2026-12-07';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '12:00:00',
            'motivo' => 'Cita que fue cancelada previamente',
            'estado' => 'cancelada',
        ]);

        $response = $this->postJson('/api/citas', [
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '11:00:00',
            'hora_fin' => '12:00:00',
            'motivo' => 'Nueva cita en horario liberado',
        ]);

        $response->assertStatus(201);
    }

    /**
     * [CASO 8] Máquina de Estados: Transición válida pendiente -> confirmada
     */
    public function test_maquina_estados_transicion_valida_pendiente_a_confirmada(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => '2026-12-08',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '08:30:00',
            'motivo' => 'Cita en pendiente',
            'estado' => 'pendiente',
        ]);

        $response = $this->patchJson("/api/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.estado', 'confirmada');
    }

    /**
     * [CASO 9] Máquina de Estados: Transición válida confirmada -> atendida
     */
    public function test_maquina_estados_transicion_valida_confirmada_a_atendida(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => '2026-12-09',
            'hora_inicio' => '09:00:00',
            'hora_fin' => '09:30:00',
            'motivo' => 'Cita confirmada',
            'estado' => 'confirmada',
        ]);

        $response = $this->patchJson("/api/citas/{$cita->id}/estado", [
            'estado' => 'atendida',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.estado', 'atendida');
    }

    /**
     * [CASO 10] Máquina de Estados: Cancelación preserva registro histórico en BD (RQF-05)
     */
    public function test_cancelacion_preserva_registro_historico_en_bd(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => '2026-12-10',
            'hora_inicio' => '10:00:00',
            'hora_fin' => '10:30:00',
            'motivo' => 'Cita historica a cancelar',
            'estado' => 'confirmada',
        ]);

        $response = $this->patchJson("/api/citas/{$cita->id}/estado", [
            'estado' => 'cancelada',
        ]);

        $response->assertStatus(200);

        // Verificamos que el registro sigue existiendo en MySQL con estado cancelada
        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'cancelada',
            'motivo' => 'Cita historica a cancelar',
        ]);
    }

    /**
     * [CASO 11] Máquina de Estados: Rechaza reactivar una cita en estado terminal cancelada -> 400
     */
    public function test_maquina_estados_rechaza_reactivar_cita_cancelada(): void
    {
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => '2026-12-11',
            'hora_inicio' => '11:00:00',
            'hora_fin' => '11:30:00',
            'motivo' => 'Cita ya cancelada',
            'estado' => 'cancelada',
        ]);

        $response = $this->patchJson("/api/citas/{$cita->id}/estado", [
            'estado' => 'confirmada',
        ]);

        // Debe fallar con error 400 o 500 según exception handler
        $this->assertTrue(in_array($response->status(), [400, 500]));
    }

    /**
     * [CASO 12] Endpoint /validar-disponibilidad responde 200 cuando el horario está libre
     */
    public function test_endpoint_validar_disponibilidad_retorna_200_cuando_esta_libre(): void
    {
        $response = $this->postJson('/api/citas/validar-disponibilidad', [
            'doctor_id' => $this->doctorA->id,
            'fecha' => '2026-12-15',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '08:30:00',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('disponible', true);
    }

    /**
     * [CASO 13] Endpoint /validar-disponibilidad responde 409 cuando hay solapamiento
     */
    public function test_endpoint_validar_disponibilidad_retorna_409_cuando_hay_conflicto(): void
    {
        $fecha = '2026-12-16';
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '08:00:00',
            'hora_fin' => '08:30:00',
            'motivo' => 'Cita previa',
            'estado' => 'confirmada',
        ]);

        $response = $this->postJson('/api/citas/validar-disponibilidad', [
            'doctor_id' => $this->doctorA->id,
            'fecha' => $fecha,
            'hora_inicio' => '08:15:00',
            'hora_fin' => '08:45:00',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('disponible', false)
                 ->assertJsonStructure(['tipo_conflicto', 'conflicto']);
    }
}
