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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->string('name');
            $table->integer('number');
            $table->integer('extra_bed');
            $table->enum('state', ['reservada', 'lliure', 'ocupada','bloquejada'])->default('lliure');
            $table->unsignedBigInteger('hotel_id');
            $table->foreign('type_id')->references('id')->on('type_rooms');
            $table->foreign('hotel_id')->references('id')->on('hotels');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
