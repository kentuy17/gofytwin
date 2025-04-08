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
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('merchant_order_num')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('action')->nullable();
            $table->string('order')->nullable();
            $table->float('amount', 8, 2)->index();
            $table->string('gateway')->nullable();
            $table->string('merchant_order_remark')->nullable();
            $table->string('status')->nullable();
            $table->string('sign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topups');
    }
};
