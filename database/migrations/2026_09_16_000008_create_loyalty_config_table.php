<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty.config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->unsignedSmallInteger('sellos_requeridos')->default(7);
            $table->unsignedSmallInteger('qr_token_segundos')->default(60); // vida útil del QR dinámico
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique('barberia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty.config');
    }
};
