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
        Schema::create('level_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_level_id');
            $table->unsignedBigInteger('direct_bv')->nullable()->default(0);
            $table->unsignedBigInteger('sponsored_bv')->nullable()->default(0);
            $table->unsignedBigInteger('sponsored_count')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('commission_level_id')->references('id')->on('commission_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_requirements');
    }
};

