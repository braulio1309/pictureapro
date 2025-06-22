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
        Schema::table('calendars', function (Blueprint $table) {
            $table->boolean('show_busy')->default(false);
            $table->boolean('require_address')->default(false);
            $table->boolean('require_nif_document')->default(false);
            $table->boolean('require_assistants')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('show_busy');
            $table->dropColumn('require_address');
            $table->dropColumn('require_nif_document');
            $table->dropColumn('require_assistants');
        });
    }
};
