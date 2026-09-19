<?php

namespace Database\Seeders;

use App\Models\Cita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $citas = [
            [
                'id' => 1,
                'paciente_id' => 1,
                'doctor_id' => 1,
                'fecha' => $today->format('Y-m-d'),
                'hora_inicio' => '08:00:00',
                'hora_fin' => '08:45:00',
                'motivo' => 'Chequeo general anual y toma de presión',
                'estado' => 'confirmada',
            ],
            [
                'id' => 2,
                'paciente_id' => 2,
                'doctor_id' => 2,
                'fecha' => $today->format('Y-m-d'),
                'hora_inicio' => '09:00:00',
                'hora_fin' => '10:00:00',
                'motivo' => 'Evaluación de electrocardiograma de control',
                'estado' => 'pendiente',
            ],
            [
                'id' => 3,
                'paciente_id' => 3,
                'doctor_id' => 3,
                'fecha' => $today->format('Y-m-d'),
                'hora_inicio' => '10:30:00',
                'hora_fin' => '11:15:00',
                'motivo' => 'Control pediátrico y esquema de vacunación',
                'estado' => 'atendida',
            ],
            [
                'id' => 4,
                'paciente_id' => 4,
                'doctor_id' => 4,
                'fecha' => $today->copy()->addDay()->format('Y-m-d'),
                'hora_inicio' => '09:00:00',
                'hora_fin' => '09:45:00',
                'motivo' => 'Consulta dermatológica por dermatitis atópica',
                'estado' => 'confirmada',
            ],
            [
                'id' => 5,
                'paciente_id' => 5,
                'doctor_id' => 5,
                'fecha' => $today->copy()->addDay()->format('Y-m-d'),
                'hora_inicio' => '11:00:00',
                'hora_fin' => '12:00:00',
                'motivo' => 'Limpieza dental profunda y profilaxis',
                'estado' => 'pendiente',
            ],
            [
                'id' => 6,
                'paciente_id' => 6,
                'doctor_id' => 1,
                'fecha' => $today->copy()->addDays(2)->format('Y-m-d'),
                'hora_inicio' => '14:00:00',
                'hora_fin' => '14:45:00',
                'motivo' => 'Revisión de resultados de laboratorio',
                'estado' => 'pendiente',
            ],
            [
                'id' => 7,
                'paciente_id' => 1,
                'doctor_id' => 2,
                'fecha' => $today->copy()->subDay()->format('Y-m-d'),
                'hora_inicio' => '15:00:00',
                'hora_fin' => '16:00:00',
                'motivo' => 'Cita cancelada por motivos de viaje del paciente',
                'estado' => 'cancelada',
            ],
            [
                'id' => 8,
                'paciente_id' => 2,
                'doctor_id' => 3,
                'fecha' => $today->copy()->addDays(3)->format('Y-m-d'),
                'hora_inicio' => '16:00:00',
                'hora_fin' => '16:45:00',
                'motivo' => 'Valoración pediátrica de crecimiento',
                'estado' => 'confirmada',
            ],
        ];

        foreach ($citas as $c) {
            Cita::updateOrCreate(['id' => $c['id']], $c);
        }
    }
}
