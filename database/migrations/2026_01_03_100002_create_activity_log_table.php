<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('clinica_id')->nullable();
            $table->string('usuario_nombre')->nullable();
            $table->string('accion');
            $table->string('descripcion')->nullable();
            $table->string('modelo')->nullable();
            $table->string('modelo_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->index(['created_at']);
            $table->index(['clinica_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
