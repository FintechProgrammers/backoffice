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
        Schema::create('nexio_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('recipient_id')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_recipient_ref')->nullable();
            $table->string('payout_account_id')->nullable();
            $table->string('recipient_ref')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nexio_users');
    }
};
