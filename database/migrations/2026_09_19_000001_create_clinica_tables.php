<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pacientes')) {
            Schema::create('pacientes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 120);
                $table->string('telefono', 25)->nullable();
                $table->string('email', 120)->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('doctores')) {
            Schema::create('doctores', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 120);
                $table->string('especialidad', 100);
                $table->string('telefono', 25)->nullable();
                $table->string('email', 120)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('citas')) {
            Schema::create('citas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('doctor_id')->constrained('doctores')->cascadeOnUpdate()->restrictOnDelete();
                $table->date('fecha');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->text('motivo');
                $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'atendida'])->default('pendiente');
                $table->timestamps();

                $table->index(['doctor_id', 'fecha'], 'idx_citas_doctor_fecha');
                $table->index('paciente_id', 'idx_citas_paciente');
                $table->index('estado', 'idx_citas_estado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
        Schema::dropIfExists('doctores');
        Schema::dropIfExists('pacientes');
    }
};
