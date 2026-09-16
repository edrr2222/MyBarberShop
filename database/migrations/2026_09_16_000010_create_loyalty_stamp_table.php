<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty.stamp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('loyalty.card')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('barberia.empleado')->cascadeOnDelete();
            // sede_id se guarda aquí (aunque la tarjeta sea por barbería) para poder
            // reportar en qué sede se generó cada sello.
            $table->foreignId('sede_id')->constrained('tenant.sede')->cascadeOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('barberia.servicio')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty.stamp');
    }
};
