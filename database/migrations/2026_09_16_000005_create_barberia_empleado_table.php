<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barberia.empleado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('tenant.sede')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('foto_url')->nullable();
            $table->text('descripcion')->nullable(); // bio corta para su perfil público
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barberia.empleado');
    }
};
