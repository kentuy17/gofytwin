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
        /**
         * P - PENDING (default)
         * W - WIN
         * L - LOSE
         * D - DRAW
         * C - CANCELLED
         */
        Schema::create('bet_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('bet_id');
            $table->integer('user_id');
            $table->integer('fight_id');
            $table->integer('fight_no');
            $table->enum('status', ['P','W','L','D','C'])->default('P');
            $table->enum('side', ['M','W']);
            $table->float('percent', 8, 2);
            $table->float('amount', 8, 2);
            $table->float('points_before_bet', 8, 2);
            $table->float('points_after_bet', 8, 2);
            $table->float('win_amount', 8, 2)->default(0);
            $table->float('current_points', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_histories');
    }
};
