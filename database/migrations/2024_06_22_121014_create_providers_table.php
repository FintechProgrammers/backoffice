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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('image_url')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('is_crypto')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('can_payin')->default(false);
            $table->boolean('can_payout')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
