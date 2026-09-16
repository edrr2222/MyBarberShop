<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant.sede', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('slug');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            // El slug de sede debe ser único dentro de cada barbería (no global)
            $table->unique(['barberia_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant.sede');
    }
};
