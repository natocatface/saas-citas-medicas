<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->date('fecha');
            $table->string('diagnostico')->nullable();
            $table->text('indicaciones')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('receta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->cascadeOnDelete();
            $table->string('medicamento');
            $table->string('dosis')->nullable();
            $table->string('frecuencia')->nullable();
            $table->string('duracion')->nullable();
            $table->string('indicaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_items');
        Schema::dropIfExists('recetas');
    }
};
