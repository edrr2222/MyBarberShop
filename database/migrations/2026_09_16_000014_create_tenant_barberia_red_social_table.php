<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant.barberia_red_social', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->string('plataforma'); // instagram | facebook | tiktok | whatsapp | web | otro
            $table->string('url');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant.barberia_red_social');
    }
};
