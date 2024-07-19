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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('user_id');
            $table->foreignId('associated_user_id');
            $table->enum('action', ['debit', 'credit']);
            $table->bigInteger('amount');
            $table->bigInteger('opening_balance')->default(0);
            $table->bigInteger('closing_balance')->default(0);
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->enum('type', ['commission', 'bonus', 'purchase', 'withdrawal']);
            $table->longText('narration')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};