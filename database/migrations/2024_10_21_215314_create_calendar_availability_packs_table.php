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
        Schema::create('calendar_availability_packs', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_availability_id');
            $table->unsignedBigInteger('service_pack_id');

            $table->foreign('calendar_availability_id')
                ->references('id')
                ->on('calendar_availabilities')
                ->cascadeOnDelete();

            $table->foreign('service_pack_id')
                ->references('id')
                ->on('service_packs')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_availability_packs');
    }
};
