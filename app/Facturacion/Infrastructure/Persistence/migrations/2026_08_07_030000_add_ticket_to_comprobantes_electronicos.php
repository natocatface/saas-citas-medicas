<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes_electronicos', function (Blueprint $table) {
            // Ticket devuelto por SUNAT en procesos asíncronos (Comunicación de
            // Baja / Resumen). Se usa para consultar el estado posteriormente.
            $table->string('ticket', 50)->nullable()->after('hash_cpe');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes_electronicos', function (Blueprint $table) {
            $table->dropColumn('ticket');
        });
    }
};
