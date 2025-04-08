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
        Schema::create('fights', function (Blueprint $table) {
            $table->id();
            $table->integer('fight_no');
            $table->integer('user_id');
            $table->enum('status',['WAITING','OPEN','CLOSED','DONE'])->default('WAITING');
            $table->string('game_winner');
            $table->integer('event_id');
            $table->time('time_start')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fights');
    }
};
