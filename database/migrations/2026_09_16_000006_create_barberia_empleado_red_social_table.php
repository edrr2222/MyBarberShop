<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barberia.empleado_red_social', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('barberia.empleado')->cascadeOnDelete();
            $table->string('plataforma'); // instagram | tiktok | facebook | whatsapp | otro
            $table->string('url');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barberia.empleado_red_social');
    }
};
