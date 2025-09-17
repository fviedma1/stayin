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
        Schema::create('type_room_service', function (Blueprint $table) {
            $table->id(); // Clave primaria
            $table->unsignedBigInteger('type_room_id'); // Relación con tipos de habitación
            $table->foreign('type_room_id')->references('id')->on('type_rooms')->onDelete('cascade');

            $table->unsignedBigInteger('service_id'); // Relación con servicios
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

            $table->timestamps(); // Opcional, para marcas de tiempo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_room_service', function (Blueprint $table) {
            $table->dropForeign(['type_room_id']);
            $table->dropForeign(['service_id']);
        });
        Schema::dropIfExists('type_room_service');
    }
};
