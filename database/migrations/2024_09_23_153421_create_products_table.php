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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->enum('type', App\Enums\ProductType::toArray())->default(App\Enums\ProductType::ALBUM);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('min_photos')->nullable();
            $table->integer('max_photos')->nullable();
            $table->integer('min_pages')->nullable();
            $table->integer('max_pages')->nullable();
            $table->decimal('page_price', 10, 2)->nullable();
            $table->integer('group_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
