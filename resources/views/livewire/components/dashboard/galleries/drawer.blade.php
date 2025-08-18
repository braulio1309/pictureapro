<div>
    <x-drawer
        id="gallery-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="{{ $action === 'edit' ? 'Editando Galería' : 'Creando Nueva Galería' }}"
        separator>
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                @click="$wire.open = false" />
            @if($action === 'edit')
            <x-button
                label="Eliminar"
                icon="o-trash"
                class="btn-accent btn-outline rounded-3xl !uppercase"
                wire:click="delete"
                spinner="delete"
                wire:target="cover,photos,delete,deletePhoto,submit"
                wire:loading.attr="disabled" />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
                wire:target="cover,photos,delete,deletePhoto,submit"
                wire:loading.attr="disabled" />
        </x-slot:actions>

        <div class="">
            <x-form wire:submit.prevent="submit" class="gap-5">

                <x-tabs
                    wire:model="tab_selected"
                    active-class="btn !btn-accent rounded-3xl space-x-5 !text-white"
                    label-class="btn btn-secondary rounded-3xl text-white text-md font-bold"
                    label-div-class="bg-white space-x-1.5">
                    <x-tab name="general" label="General" icon="s-cog-6-tooth">
                        <div class="space-y-7">
                            <div wire:key="gallery-cover-{{ $cover_key }}">
                                <x-file
                                    label="Portada"
                                    wire:model="cover"
                                    change-text="Seleccionar imagen"
                                    accept="image/png, image/jpeg"
                                    class="w-2/3 md:w-full max-w-full">
                                    <img src="{{ $this->coverThumbnail }}" alt="Portada" class="w-60 rounded-3xl">
                                </x-file>
                                <p wire:loading wire:target="cover" class="text-sm text-gray-700 italic">Subiendo imagen...</p>
                            </div>

                            <div>
                                <div wire:key="gallery-images-{{ $photos_key }}">
                                    <x-file wire:model="photos" label="Fotos de la galería" multiple />
                                    <p wire:loading wire:target="photos" class="text-sm text-gray-700 italic">Subiendo imagenes...</p>
                                </div>
                                @if(!empty($this->photosThumbnails))
                                <div class="grid grid-cols-5 gap-3 mt-3">
                                    @foreach ($this->photosThumbnails as $photo)
                                    <div class="col-span-1">
                                        <x-card class="shadow hover:scale-105 transition-all">
                                            <x-button
                                                icon="s-trash"
                                                class="btn-sm btn-circle btn-accent btn-outline float-end"
                                                wire:click="deletePhoto('{{ $photo['id'] }}')" />
                                            <x-slot:figure>
                                                <img src="{{ $photo['url'] }}" alt="Foto de galería" />
                                            </x-slot:figure>
                                        </x-card>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <x-input
                                label="Título interno"
                                class="rounded-3xl max-w-full"
                                wire:model="form.internal_title"
                                required />

                            <x-input
                                label="Título para el cliente"
                                class="rounded-3xl max-w-full"
                                wire:model="form.client_title"
                                required />

                            <x-textarea
                                label="Descripción interna"
                                class="rounded-3xl"
                                wire:model="form.internal_description"
                                rows="3" />

                            <x-textarea
                                label="Descripción para el cliente"
                                class="rounded-3xl"
                                wire:model="form.client_description"
                                rows="3" />

                            <x-toggle
                                label="Activo"
                                class="rounded-3xl"
                                wire:model="form.is_active" />
                        </div>
                    </x-tab>
                    <x-tab name="productos" label="Productos" icon="o-shopping-bag">
                        <div class="space-y-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Productos incluidos</h3>
                                <x-button
                                    label="Completa tu sesión"
                                    class="btn-primary rounded-3xl"
                                    icon="o-plus" />
                            </div>

                            <div class="space-y-4">
                                <x-checkbox
                                    label="Incluir botón 'Completa tu sesión' en vista cliente"
                                    wire:model="form.show_complete_session_button"
                                    class="rounded-3xl" />

                                <div class="divider">Productos incluidos en el precio</div>

                                @foreach($availableProducts as $product)
                                <div class="flex items-center justify-between p-3 border rounded-lg">
                                    <div>
                                        <h4 class="font-medium">{{ $product['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $product['description'] }}</p>
                                        <span class="text-sm font-semibold">{{ number_format($product['price'], 2) }} €</span>
                                    </div>
                                    <x-toggle
                                        wire:click="toggleProduct({{ $product['id'] })"
                        :checked="in_array($product['id'], $includedProducts)"
                        class="rounded-3xl" />
                </div>
            @endforeach
            
            @empty($availableProducts)
                <p class="text-gray-500 text-center py-4">No hay productos disponibles</p>
            @endempty
            
            <div class="divider">Álbum productos incluidos</div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border p-4 rounded-lg">
                    <h4 class="font-medium">Álbum 20x20</h4>
                    <p class="text-sm text-gray-600 mt-2">
                        Selecciona 10 fotografías de las que ha seleccionado para la galería
                    </p>
                    <x-input
                        type="number"
                        min="0"
                        wire:model="form.album_20x20_photos"
                        class="rounded-3xl mt-2"
                        placeholder="Número de fotos" />
                </div>
                
                <div class="border p-4 rounded-lg">
                    <h4 class="font-medium">Lienzo 60x40</h4>
                    <p class="text-sm text-gray-600 mt-2">
                        Producto incluido en plantillas de galería
                    </p>
                    <x-toggle
                        wire:model="form.includes_canvas_60x40"
                        class="rounded-3xl mt-2" />
                </div>
            </div>
        </div>
    </div>
</x-tab>

                    <x-tab name="opciones" label="Opciones" icon="s-cog-6-tooth">
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <x-input
                                    label="Número de fotos a seleccionar"
                                    type="number"
                                    wire:model="form.photos_to_select"
                                    class="rounded-3xl" />

                                <x-input
                                    label="Máximo de fotos a seleccionar (0 = sin límite)"
                                    type="number"
                                    wire:model="form.max_photos_to_select"
                                    class="rounded-3xl" />
                            </div>

                            <x-input
                                label="Días de caducidad"
                                type="number"
                                wire:model="form.expiration_days"
                                class="rounded-3xl" />

                            <x-select
                                label="Permitir descarga de galería"
                                wire:model="form.download_option"
                                class="rounded-3xl"
                                :options="[
            ['id' => 'after_payment', 'name' => 'Permitir después del pago'],
            ['id' => 'never', 'name' => 'No permitir descarga'],
            ['id' => 'always', 'name' => 'Permitir siempre']]" />



                            <x-radio
                                label="El cliente va a descargar:"
                                wire:model="form.download_selected_only"
                                :options="[
                                    ['id' => 1, 'name' => 'Solo las fotografías seleccionadas'],
                                   ['id' => 0, 'name' => 'Todas las fotografias']
                                ]"
                                class="rounded-3xl" />
r
                            <x-toggle
                                label="Incrustar marca de agua"
                                wire:model="form.watermark_enabled"
                                class="rounded-3xl" />

                            <x-toggle
                                label="Mostrar nombres de archivo"
                                wire:model="form.show_filenames"
                                class="rounded-3xl" />

                            <x-select
                                label="Permitir comentarios"
                                wire:model="form.comments_option"
                                class="rounded-3xl"
                                :options=" [
            ['id' => 'always', 'name' => 'Siempre'],
            ['id' => 'selected_only', 'name' => 'Solo fotos seleccionadas'],
            ['id' => 'unselected_only', 'name' => 'Solo fotos no seleccionadas'],
            ['id' => 'never', 'name' => 'Nunca'],
        ]" />
                        </div>
                    </x-tab>

                    <x-tab name="precios" label="Precios" icon="s-currency-dollar">
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <x-input
                                    label="Precio de la sesión"
                                    type="number"
                                    step="0.01"
                                    wire:model="form.session_price"
                                    class="rounded-3xl" />

                                <x-input
                                    label="Precio por foto adicional"
                                    type="number"
                                    step="0.01"
                                    wire:model="form.additional_photo_price"
                                    class="rounded-3xl" />

                                <x-input
                                    label="Precio galería completa"
                                    type="number"
                                    step="0.01"
                                    wire:model="form.full_gallery_price"
                                    class="rounded-3xl" />
                            </div>

                            <div class="divider">Paquetes de fotos</div>

                            @foreach($photo_packages as $index => $package)
                            <div class="flex items-center gap-3">
                                <x-input
                                    label="Número de fotos"
                                    type="number"
                                    wire:model="photo_packages.{{ $index }}.quantity"
                                        class="rounded-3xl" />

                                    <x-input
                                        label="Precio"
                                        type="number"
                                        step="0.01"
                                        wire:model="photo_packages.{{ $index }}.price"
                                        class="rounded-3xl" />

                                    <x-button
                                        icon="o-trash"
                                        class="btn-error btn-circle mt-6"
                                        wire:click="removePhotoPackage({{ $index }})" />
                                </div>
                                @endforeach

                                <div class="flex items-end gap-3">
                                    <x-input
                                        label="Nuevo paquete - Cantidad"
                                        type="number"
                                        wire:model="new_package_quantity"
                                        class="rounded-3xl" />

                                    <x-input
                                        label="Nuevo paquete - Precio"
                                        type="number"
                                        step="0.01"
                                        wire:model="new_package_price"
                                        class="rounded-3xl" />

                                    <x-button
                                        label="Añadir"
                                        class="btn-primary rounded-3xl"
                                        wire:click="addPhotoPackage" />
                                </div>
                            </div>
                    </x-tab>

                    <x-tab name="pago" label="Pago" icon="s-credit-card">
                        <div class="space-y-5">
                            <x-toggle
                                label="Aceptar pago en efectivo"
                                wire:model="form.cash_payment_enabled"
                                class="rounded-3xl" />

                            <x-toggle
                                label="Aceptar transferencia bancaria"
                                wire:model="form.bank_transfer_enabled"
                                class="rounded-3xl" />

                            <div class="divider">Emails automáticos</div>

                            <x-input
                                label="Asunto email confirmación pago"
                                wire:model="form.payment_email_subject"
                                class="rounded-3xl" />

                            <x-textarea
                                label="Cuerpo email confirmación pago"
                                wire:model="form.payment_email_body"
                                class="rounded-3xl"
                                rows="3" />

                            <x-input
                                label="Asunto email confirmación pago manual"
                                wire:model="form.manual_payment_confirmation_subject"
                                class="rounded-3xl" />

                            <x-textarea
                                label="Cuerpo email confirmación pago manual"
                                wire:model="form.manual_payment_confirmation_body"
                                class="rounded-3xl"
                                rows="3" />

                            <x-input
                                label="Asunto email confirmación selección"
                                wire:model="form.photo_selection_confirmation_subject"
                                class="rounded-3xl" />

                            <x-textarea
                                label="Cuerpo email confirmación selección"
                                wire:model="form.photo_selection_confirmation_body"
                                class="rounded-3xl"
                                rows="3" />
                        </div>
                    </x-tab>
                </x-tabs>
            </x-form>
        </div>
    </x-drawer>
</div>