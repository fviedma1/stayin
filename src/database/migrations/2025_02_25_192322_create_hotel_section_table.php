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
        Schema::create('hotel_section', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('section_id');
            $table->boolean('is_visible')->default(true);
            $table->unsignedBigInteger('order')->default(1);
            $table->unsignedBigInteger('display_count')->nullable();
            $table->timestamps();

            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_section');
    }
};
