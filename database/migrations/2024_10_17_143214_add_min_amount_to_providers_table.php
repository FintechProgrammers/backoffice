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
        Schema::table('providers', function (Blueprint $table) {
            $table->double('min_amount', 8, 2)->nullable(false)->after('can_payout');
            $table->double('max_amount', 8, 2)->nullable(false)->after('min_amount');
            $table->double('charge', 8, 2)->nullable(false)->after('max_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['min_amount', 'max_amount', 'charge']);
        });
    }
};
