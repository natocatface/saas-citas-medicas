<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('cie10', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinica_id')->nullable()->index();
            $table->string('codigo', 20);
            $table->string('descripcion');
            $table->string('categoria')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['codigo']);
        });
    }
    public function down(): void { Schema::dropIfExists('cie10'); }
};
