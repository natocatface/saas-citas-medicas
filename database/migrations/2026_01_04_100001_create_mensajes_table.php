<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinica_id')->nullable()->index();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto');
            $table->text('cuerpo');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();
            $table->index(['destinatario_id', 'leido']);
        });
    }
    public function down(): void { Schema::dropIfExists('mensajes'); }
};
