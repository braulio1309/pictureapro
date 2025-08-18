<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            
            // Información básica
            $table->string('internal_title'); // Título interno de la galería
            $table->string('client_title'); // Título de la galería para el cliente
            $table->text('internal_description'); // Descripción interna
            $table->text('client_description'); // Descripción para el cliente
            
            // Opciones de selección
            $table->integer('photos_to_select')->default(0); // Número de fotos a seleccionar
            $table->integer('max_photos_to_select')->default(0); // 0 = sin límite
            $table->integer('expiration_days')->default(7); // Días de caducidad
            
            // Opciones de descarga
            $table->enum('download_option', ['after_payment', 'never', 'always'])->default('after_payment');
            $table->boolean('download_selected_only')->default(true); // Solo fotos seleccionadas o todas
            
            // Precios
            $table->decimal('session_price', 10, 2)->nullable(); // Precio de la sesión
            $table->decimal('additional_photo_price', 10, 2)->default(5); // Precio por foto adicional
            $table->decimal('full_gallery_price', 10, 2)->default(20); // Precio galería completa
            
            // Opciones de visualización
            $table->boolean('watermark_enabled')->default(false); // Marca de agua
            $table->boolean('show_filenames')->default(false); // Mostrar nombres de archivo
            $table->enum('comments_option', ['always', 'selected_only', 'unselected_only', 'never'])->default('never');
            
            // Opciones de pago
            $table->boolean('cash_payment_enabled')->default(false);
            $table->boolean('bank_transfer_enabled')->default(false);
            
            // Emails
            $table->string('payment_email_subject')->nullable();
            $table->text('payment_email_body')->nullable();
            $table->string('manual_payment_confirmation_subject')->nullable();
            $table->text('manual_payment_confirmation_body')->nullable();
            $table->string('photo_selection_confirmation_subject')->nullable();
            $table->text('photo_selection_confirmation_body')->nullable();
            
            // Paquetes de fotos (podría ser una tabla separada en una relación one-to-many)
            $table->json('photo_packages')->nullable(); // Almacenaría array de paquetes {quantity, price}
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
