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
        Schema::table('bonus_histories', function (Blueprint $table) {
            $table->foreignId('cycle_id')->nullable()->after('user_id');
            $table->string('level')->nullable()->after('referee_id');
            $table->boolean('is_converted')->default(false)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_histories', function (Blueprint $table) {
            $table->dropColumn(['cycle_id','level','is_converted']);
        });
    }
};
