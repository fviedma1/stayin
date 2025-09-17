<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelConfigsTable extends Migration
{
    public function up()
{
    Schema::create('hotel_configs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
        $table->integer('news_limit')->default(5);
        $table->integer('comments_limit')->default(5);
        $table->json('sections_order')->default(json_encode([
            'Habitacions' => 1,
            'Noticies' => 2,
            'Feedbacks' => 3
        ]));
        $table->json('sections_visibility')->default(json_encode([
            'Habitacions' => 1,
            'Noticies' => 1,
            'Feedbacks' => 1
        ]));
        $table->timestamps();
    });
}
    public function down()
    {
        Schema::dropIfExists('hotel_configs');
    }
}