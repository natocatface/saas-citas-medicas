<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinicas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('planes')->nullOnDelete();
            $table->enum('estado_suscripcion', ['prueba', 'activa', 'suspendida', 'cancelada'])->default('prueba');
            $table->date('suscripcion_vence')->nullable();
            $table->string('color', 20)->default('#17b8cf');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinicas');
    }
};
