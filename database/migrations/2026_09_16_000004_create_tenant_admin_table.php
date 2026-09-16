<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant.admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            // sede_id nulo = admin de toda la barbería; con valor = admin limitado a una sede
            $table->foreignId('sede_id')->nullable()->constrained('tenant.sede')->nullOnDelete();
            $table->string('nombre');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('rol')->default('admin_barberia'); // admin_barberia | admin_sede
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant.admin');
    }
};
