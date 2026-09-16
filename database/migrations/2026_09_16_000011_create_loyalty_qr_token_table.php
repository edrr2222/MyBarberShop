<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty.qr_token', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('tenant.client')->cascadeOnDelete();
            $table->foreignId('barberia_id')->constrained('tenant.barberia')->cascadeOnDelete();
            $table->uuid('token')->unique();
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->index(['token', 'used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty.qr_token');
    }
};
