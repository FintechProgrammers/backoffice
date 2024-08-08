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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('amount', 10, 4)->default(0.0000)->change();
            $table->decimal('bv_amount', 10, 4)->default(0.0000)->change();
        });

        Schema::table('commission_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 4)->default(0.0000)->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price', 10, 4)->default(0.0000)->change();
            $table->decimal('bv_amount', 10, 4)->default(0.0000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};