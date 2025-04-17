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
        Schema::table('event_games', function (Blueprint $table) {
            $table->integer('current_fight')->after('status')->default(1);
            $table->integer('limit_fight')->after('status')->default(260);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_games', function (Blueprint $table) {
            $table->removeColumn('current_fight');
            $table->removeColumn('limit_fight');
        });
    }
};
