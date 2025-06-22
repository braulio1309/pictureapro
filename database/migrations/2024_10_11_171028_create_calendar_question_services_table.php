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
        Schema::create('calendar_question_services', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_question_id');
            $table->unsignedBigInteger('service_id');

            $table->foreign('calendar_question_id')->references('id')->on('calendar_questions')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_question_services');
    }
};
