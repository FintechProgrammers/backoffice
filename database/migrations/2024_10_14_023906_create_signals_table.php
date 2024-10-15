<?php

use App\Models\Asset;
use App\Models\Category;
use App\Models\Streamer;
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
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignIdFor(Streamer::class, 'streamer_id');
            $table->foreignIdFor(Category::class, 'category_id');
            $table->string('asset_type')->nullable();
            $table->string('order_type')->nullable();
            $table->string('entry_price')->default(0);
            $table->string('stop_loss')->default(0);
            $table->string('target_price')->default(0);
            $table->string('percentage')->default(0);
            $table->longText('comment')->nullable();
            $table->string('photo')->nullable();
            $table->string('chart_photo')->nullable();
            $table->string('file_url')->nullable();
            $table->string('market_status')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_updated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
