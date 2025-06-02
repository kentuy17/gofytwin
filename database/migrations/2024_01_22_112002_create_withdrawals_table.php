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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('reference_no')->index()->nullable()->index();
            $table->string('contact_no')->index()->nullable()->index();
            $table->string('receipt')->nullable()->index();
            $table->float('amount', 8, 2)->index();
            $table->string('claimed')->default(0)->index();
            $table->string('upload')->nullable();
            $table->string('approved')->default(0)->index();
            $table->string('payout_remittance')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('receiver_mobile_number')->nullable();
            $table->string('merchant_order_no')->nullable();
            $table->string('claimed_date')->nullable();
            $table->string('process_status')->default('pending')->index();
            $table->string('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
