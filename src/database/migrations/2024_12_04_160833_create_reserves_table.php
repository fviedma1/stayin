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
        Schema::create('reserves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id')->index(); // Relación con las habitaciones
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index(); // Relación con los usuarios
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->integer('people')->nullable();
            $table->string('notes')->nullable();
            $table->date('date_in');
            $table->date('date_out');

            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            
            $table->enum('status', ['pendent', 'checkin', 'checkout', 'cancelada'])->default('pendent'); // Estado de la reserva
            $table->float('price'); // Precio de la reserva

            $table->timestamps(); // Fecha de creación y última actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserves');
    }
};
