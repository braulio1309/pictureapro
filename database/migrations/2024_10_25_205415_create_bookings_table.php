<?php

use App\Enums\BookingStatus;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calendar_id');
            $table->unsignedBigInteger('calendar_availability_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('service_pack_id');
            $table->enum('status', BookingStatus::toArray())->default(BookingStatus::PENDING);
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('allow_share')->default(false);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('calendar_id')->references('id')->on('calendars')->cascadeOnDelete();
            $table->foreign('calendar_availability_id')->references('id')->on('calendar_availabilities')->cascadeOnDelete();
            $table->foreign('client_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('service_pack_id')->references('id')->on('service_packs')->cascadeOnDelete();
            $table->foreign('source_id')->references('id')->on('sources')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
