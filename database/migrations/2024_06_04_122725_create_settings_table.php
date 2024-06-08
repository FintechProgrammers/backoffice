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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('withdrawal_is_active')->default(false);
            $table->unsignedBigInteger('minimum_withdrawal_amount')->default(0);
            $table->unsignedBigInteger('maximum_withdrawal_amount')->default(0);
            $table->unsignedBigInteger('withdrawal_fee')->default(0);
            $table->unsignedBigInteger('bv_equivalent')->default(0);
            $table->unsignedBigInteger('ambassador_fee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
