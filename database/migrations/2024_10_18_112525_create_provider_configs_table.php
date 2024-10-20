<?php

use App\Models\Provider;
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
        Schema::create('provider_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Provider::class, 'provider_id');
            $table->longText('api_key')->nullable();
            $table->longText('secret')->nullable();
            $table->longText('username')->nullable();
            $table->longText('password')->nullable();
            $table->longText('merchant_id')->nullable();
            $table->longText('account_id')->nullable();
            $table->longText('webhook_secret')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_configs');
    }
};
