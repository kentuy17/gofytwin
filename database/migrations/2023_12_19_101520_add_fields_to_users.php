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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile_no')->index()->nullable();
            $table->string('location')->index()->nullable();
            $table->string('percentage')->nullable();
            $table->string('account_status')->nullable()->index();
            $table->string('ip')->nullable()->index();
            $table->string('user_attempts')->default(0);
            $table->string('user_level')->default(0);
            $table->string('is_referral')->default(0)->index();
            $table->string('website_url')->nullable();
            $table->string('is_new')->default(0)->nullable();
            $table->string('website_register')->nullable();
            $table->string('bet_limit')->default(0);
            $table->float('credits', 10, 2)->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
