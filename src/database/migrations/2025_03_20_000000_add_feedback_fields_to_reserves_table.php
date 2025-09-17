<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reserves', function (Blueprint $table) {
            $table->string('feedback_token')->nullable();
            $table->timestamp('feedback_token_expires_at')->nullable();
            $table->timestamp('feedback_submitted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('reserves', function (Blueprint $table) {
            $table->dropColumn([
                'feedback_token',
                'feedback_token_expires_at',
                'feedback_submitted_at'
            ]);
        });
    }
}; 