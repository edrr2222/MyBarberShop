<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barberia.servicio', function (Blueprint $table) {
            // corte | barba | color | otro — agrupa el menú de servicios que ve el cliente
            $table->string('categoria', 20)->default('otro')->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('barberia.servicio', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }
};
