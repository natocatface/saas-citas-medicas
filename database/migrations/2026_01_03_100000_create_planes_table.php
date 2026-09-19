<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio', 10, 2)->default(0);
            $table->string('periodo')->default('mensual');
            $table->unsignedInteger('max_medicos')->nullable();
            $table->unsignedInteger('max_usuarios')->nullable();
            $table->text('caracteristicas')->nullable();
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
