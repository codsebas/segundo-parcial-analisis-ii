<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Suite de Pruebas: Interfaz FullCalendar UI y Renderizado (RQF-02, RQF-10, RQNF-06)
 */
class CalendarUiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [TEST 1] La ruta raíz '/' responde HTTP 200 y carga la vista con FullCalendar y tipografías
     */
    public function test_ruta_raiz_carga_vista_calendario_con_recursos(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('FullCalendar');
        $response->assertSee('Raleway');
        $response->assertSee('Open Sans');
        $response->assertSee('modalCrearCita');
        $response->assertSee('modalDetalleCita');
        $response->assertSee('filtroDoctor');
        $response->assertSee('filtroEstado');
    }

    /**
     * [TEST 2] Verificación de la paleta de colores requerida en CitaResource (RQF-10)
     */
    public function test_citas_api_retorna_colores_de_estado_especificados_incluyendo_rojo_cancelada(): void
    {
        $doctor = Doctor::firstOrCreate(['id' => 1], ['nombre' => 'Dr. Test', 'especialidad' => 'General']);
        $paciente = Paciente::firstOrCreate(['id' => 1], ['nombre' => 'Paciente Test']);

        // Crear una cita cancelada
        $citaCancelada = Cita::create([
            'paciente_id' => $paciente->id,
            'doctor_id' => $doctor->id,
            'fecha' => '2026-12-28',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '08:45:00',
            'motivo' => 'Cita Cancelada Test Colores',
            'estado' => 'cancelada',
        ]);

        $response = $this->getJson('/api/citas?estado=cancelada');

        $response->assertStatus(200);
        // Debe contener el color rojo pastel solicitado: #fee2e2 (bg) y #ef4444 (border)
        $response->assertJsonFragment([
            'backgroundColor' => '#fee2e2',
            'borderColor' => '#ef4444',
            'textColor' => '#991b1b',
        ]);
    }
}
