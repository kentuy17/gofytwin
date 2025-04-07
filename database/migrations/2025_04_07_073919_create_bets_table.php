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
         * D - DONE
         * X - DRAW
         * F - FIGHTING
         * C - CANCELLED
         */
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->integer('fight_id');
            $table->integer('fight_no');
            $table->integer('user_id');
            $table->float('amount', 8, 2)->default('0.00');
            $table->enum('side',['M','W']);
            $table->enum('status',['D','X','F']);
            $table->float('win_amount', 8, 2)->default('0.00');
            $table->float('agent_commission', 8, 2)->default('0.00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
