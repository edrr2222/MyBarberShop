<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant.client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->string('cedula');
            $table->string('nombre');
            $table->string('password');
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // La cédula es única por barbería, no global: el mismo cliente
            // puede registrarse en distintas barberías de la plataforma.
            $table->unique(['barberia_id', 'cedula']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant.client');
    }
};
