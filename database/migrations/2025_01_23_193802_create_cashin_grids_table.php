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
        Schema::create('cashin_grids', function (Blueprint $table) {
            $table->id();
            $table->string('cashin_id')->nullable()->index();
            $table->string('user_id')->nullable()->index();
            $table->string('mobile_no')->nullable()->index();
            $table->string('merchant_order_no')->nullable()->index();
            $table->string('gateway')->nullable()->index();
            $table->string('amount')->nullable()->index();
            $table->string('sign')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashin_grids');
    }
};
