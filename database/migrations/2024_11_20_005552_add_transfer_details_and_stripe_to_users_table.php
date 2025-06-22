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
        Schema::table('users', function (Blueprint $table) {
            $table->text('transfer_details')->nullable();
            $table->string('stripe_pub')->nullable();
            $table->string('stripe_priv')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('transfer_details');
            $table->dropColumn('stripe_pub');
            $table->dropColumn('stripe_priv');
        });
    }
};
