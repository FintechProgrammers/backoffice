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
        Schema::create('user_kycs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('user_id');
            $table->enum('service', \App\Models\UserKyc::SERVICE);
            $table->string('front_link')->nullable();
            $table->string('back_link')->nullable();
            $table->json('details')->nullable();
            $table->enum('status', \App\Models\UserKyc::STATUS)->default(\App\Models\UserKyc::STATUS['pending']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kycs');
    }
};
