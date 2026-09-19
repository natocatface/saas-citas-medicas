<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->string('metodo_pago')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->index(['fecha', 'estado']);
        });

        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('cantidad', 8, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('importe', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_items');
        Schema::dropIfExists('facturas');
    }
};
