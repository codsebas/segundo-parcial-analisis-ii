<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctores = [
            ['id' => 1, 'nombre' => 'Dr. Alejandro Morales', 'especialidad' => 'Medicina General', 'telefono' => '+502 5551-1001', 'email' => 'amorales@clinica.com'],
            ['id' => 2, 'nombre' => 'Dra. Beatriz Castillo', 'especialidad' => 'Cardiología', 'telefono' => '+502 5552-2002', 'email' => 'bcastillo@clinica.com'],
            ['id' => 3, 'nombre' => 'Dr. Carlos Mendoza', 'especialidad' => 'Pediatría', 'telefono' => '+502 5553-3003', 'email' => 'cmendoza@clinica.com'],
            ['id' => 4, 'nombre' => 'Dra. Diana Herrera', 'especialidad' => 'Dermatología', 'telefono' => '+502 5554-4004', 'email' => 'dherrera@clinica.com'],
            ['id' => 5, 'nombre' => 'Dr. Eduardo Vásquez', 'especialidad' => 'Odontología', 'telefono' => '+502 5555-5005', 'email' => 'evasquez@clinica.com'],
        ];

        foreach ($doctores as $doc) {
            Doctor::updateOrCreate(['id' => $doc['id']], $doc);
        }
    }
}
