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
        Schema::create('cashins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('merchant_order_no')->nullable()->index();
            $table->string('gateway')->nullable();
            $table->float('amount', 8, 2)->index();
            $table->string('sign')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashins');
    }
};
