<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty.card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('tenant.client')->cascadeOnDelete();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->unsignedSmallInteger('sellos_actuales')->default(0);
            // activa: acumulando sellos | completada: llegó al máximo, corte gratis pendiente | redimida: cerrada
            $table->string('estado')->default('activa');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Solo puede existir una tarjeta "activa" o "completada" a la vez por cliente+barbería.
            // (se valida también a nivel de aplicación al crear una nueva tras redimir)
            $table->index(['client_id', 'barberia_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty.card');
    }
};
