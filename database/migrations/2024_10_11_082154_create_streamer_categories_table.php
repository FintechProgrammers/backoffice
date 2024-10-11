<?php

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
        Schema::create('streamer_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Streamer::class, 'streamer_id');
            $table->foreignIdFor(Category::class, 'category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamer_categories');
    }
};
