<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservation_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Email del usuario
            $table->string('token')->unique(); // Token único
            $table->timestamp('expires_at'); // Fecha de expiración del token
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_tokens');
    }
};
