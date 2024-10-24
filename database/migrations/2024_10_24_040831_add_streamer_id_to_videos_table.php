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
        Schema::table('videos', function (Blueprint $table) {
            $table->foreignId('streamer_id')->after('schedule_id');
            $table->string('thumbnail')->nullable()->after('type');
            $table->boolean('is_favourite')->default(false)->after('thumbnail');
            $table->unsignedBigInteger('order')->nullable()->after('is_favourite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['streamer_id', 'thumbnail', 'is_favourite', 'order']);
        });
    }
};
