<?php

use App\Models\Service;
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
        Schema::create('service_streamers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class, 'service_id');
            $table->foreignIdFor(Streamer::class, 'streamer_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_streamers');
    }
};
