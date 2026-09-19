<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            ['id' => 1, 'nombre' => 'Juan Pérez Gómez', 'telefono' => '+502 4441-1111', 'email' => 'jperez@gmail.com', 'fecha_nacimiento' => '1988-04-12'],
            ['id' => 2, 'nombre' => 'María Fernanda López', 'telefono' => '+502 4442-2222', 'email' => 'mflopez@gmail.com', 'fecha_nacimiento' => '1995-09-23'],
            ['id' => 3, 'nombre' => 'Roberto Carlos Soto', 'telefono' => '+502 4443-3333', 'email' => 'rsoto@gmail.com', 'fecha_nacimiento' => '1982-11-05'],
            ['id' => 4, 'nombre' => 'Lucía Gabriela Méndez', 'telefono' => '+502 4444-4444', 'email' => 'lmendez@gmail.com', 'fecha_nacimiento' => '2001-02-18'],
            ['id' => 5, 'nombre' => 'Esteban Ramos Cruz', 'telefono' => '+502 4445-5555', 'email' => 'eramos@gmail.com', 'fecha_nacimiento' => '1976-07-30'],
            ['id' => 6, 'nombre' => 'Sofía Isabel Reyes', 'telefono' => '+502 4446-6666', 'email' => 'sreyes@gmail.com', 'fecha_nacimiento' => '1999-12-14'],
        ];

        foreach ($pacientes as $pac) {
            Paciente::updateOrCreate(['id' => $pac['id']], $pac);
        }
    }
}
