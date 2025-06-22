<div>
    <x-drawer
        id="product-drawer"
        wire:model="open"
        class="w-full lg:w-2/3"
        right
        title="Configurando producto"
        separator
    >
        <x-slot:actions>
            <x-button
                label="Cerrar"
                class="btn-ghost rounded-3xl !uppercase"
                @click="$wire.open = false"
            />
            @if($action === 'edit')
                <x-button
                    label="Eliminar"
                    icon="o-trash"
                    class="btn-accent btn-outline rounded-3xl !uppercase"
                    wire:click="delete"
                    spinner="delete"
                    wire:target="cover,photos,delete,deletePhoto,submit"
                    wire:loading.attr="disabled"
                />
            @endif
            <x-button
                label="Guardar"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                wire:click="submit"
                spinner="submit"
                wire:target="cover,photos,delete,deletePhoto,submit"
                wire:loading.attr="disabled"
            />
        </x-slot:actions>

        <div class="">
            <x-form wire:submit.prevent="submit" class="gap-5">

                <x-tabs
                    wire:model="tab_selected"
                    active-class="btn !btn-accent rounded-3xl space-x-5 !text-white"
                    label-class="btn btn-secondary rounded-3xl text-white text-md font-bold"
                    label-div-class="bg-white space-x-1.5"
                >
                    <x-tab name="general" label="General" icon="s-cog-6-tooth">
                        <div class="space-y-7">
                            <div wire:key="product-cover-{{ $cover_key }}">
                                <x-file
                                    label="Portada"
                                    wire:model="cover"
                                    change-text="Seleccionar imagen"
                                    accept="image/png, image/jpeg"
                                    class="w-2/3 md:w-full max-w-full"
                                >
                                    <img src="{{ $this->coverThumbnail }}" alt="Portada" class="w-60 rounded-3xl">
                                </x-file>
                                <p wire:loading wire:target="cover" class="text-sm text-gray-700 italic">Subiendo imagen...</p>
                            </div>

                            <div>
                                <div wire:key="product-images-{{ $this->form->product->id ?? '000' }}">
                                    <x-file wire:model="photos" label="Fotos de muestra" multiple />
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
                                                        wire:click="deletePhoto('{{ $photo['id'] }}')" 
                                                    />
                                                    <x-slot:figure>
                                                        <img src="{{ $photo['url'] }}" alt="Foto de muestra" />
                                                    </x-slot:figure>
                                                </x-card>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <x-input
                                label="Titulo"
                                class="rounded-3xl max-w-full"
                                wire:model="form.name"
                                required
                            />
                    
                            <x-toggle
                                label="Activo"
                                class="rounded-3xl"
                                wire:model="form.is_active"
                            />

                            @if(in_array($this->form->type, [\App\Enums\ProductType::OTHER, \App\Enums\ProductType::NO_PHOTO]))
                                <x-toggle
                                    label="Varios tamaños"
                                    class="rounded-3xl"
                                    wire:model.live="form.has_sizes"
                                />
                            @endif

                            @if(!$this->form->has_sizes)
                                <div>
                                    <span class="label-text font-semibold">Precio <span class="text-error">*</span></span>
                                    <label class="input input-bordered input-primary @error('form.price') input-error @enderror rounded-3xl flex items-center justify-between gap-2 w-full mt-1">
                                        <input
                                            placeholder="Precio"
                                            class="w-full"
                                            type="number"
                                            wire:model="form.price"
                                            required
                                        >
                                        <span class="badge badge-lg">€</span>
                                    </label>
                                    @error('form.price') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            @if(!$this->form->has_sizes && $this->form->type === \App\Enums\ProductType::OTHER)
                                <div>
                                    <h4 class="mt-5 text-lg font-medium">Cantidades disponibles del producto</h4>
                                    <div class="grid grid-cols-2 gap-5 mt-3">
                                        <div>
                                            <x-input
                                                label="Cantidad mínima"
                                                inline
                                                class="rounded-3xl"
                                                wire:model="form.min_pages"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div>
                                            <x-input
                                                label="Cantidad máxima"
                                                inline
                                                class="rounded-3xl"
                                                wire:model="form.max_pages"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div>
                                            <x-input
                                                label="En grupos de"
                                                inline
                                                class="rounded-3xl"
                                                wire:model="form.group_by"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($this->form->type === \App\Enums\ProductType::OTHER)
                                <div>
                                    <h4 class="mt-5 text-lg font-medium">Nº de fotos</h4>
                                    <div class="grid grid-cols-2 gap-5 mt-3">
                                        <div>
                                            <x-input
                                                label="Nº mínimo de fotos"
                                                inline
                                                class="rounded-3xl"
                                                wire:model="form.min_photos"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div>
                                            <x-input
                                                label="Nº máximo de fotos"
                                                inline
                                                class="rounded-3xl"
                                                wire:model="form.max_photos"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <x-editor
                                label="Descripción"
                                class="rounded-3xl w-2/3 md:w-full"
                                wire:model="form.description"
                                :config="$tinyMCE_settings"
                            />
                        </div>
                    </x-tab>

                    @if($this->form->type === \App\Enums\ProductType::ALBUM)
                        <x-tab name="cover" label="Portada" icon="o-bookmark-square">
                            <div class="space-y-3">
                                <h4 class="mt-5 text-lg font-medium">Materiales</h4>
                                <x-choices
                                    placeholder="Buscar.."
                                    wire:model="materials"
                                    :options="$this->materialsOptions"
                                    height="max-h-96"
                                    class="rounded-3xl"
                                />
                            </div>
                        </x-tab>
                        <x-tab name="interior" label="Interior" icon="s-book-open">
                            <div class="space-y-12">
                                <div class="w-full space-y-3">
                                    <h4 class="mt-5 text-lg font-medium">Fotos incluidas en el álbum base</h4>
                                    <div class="flex justify-between space-x-5">
                                        <div class="w-full">
                                            <x-input
                                                placeholder="Nº mínimo de fotos"
                                                class="rounded-3xl"
                                                wire:model="form.min_photos"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div class="w-full">
                                            <x-input
                                                placeholder="Nº máximo de fotos"
                                                class="rounded-3xl"
                                                wire:model="form.max_photos"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full space-y-3">
                                    <h4 class="mt-5 text-lg font-medium">Pliegos extra</h4>
                                    <div class="flex justify-between space-x-5">
                                        <div class="w-full">
                                            <x-input
                                                placeholder="Nº máximo de pliegos"
                                                class="rounded-3xl"
                                                wire:model="form.max_pages"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div class="w-full">
                                            <x-input
                                                placeholder="Precio por pliego"
                                                class="rounded-3xl"
                                                wire:model="form.page_price"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                        <div class="w-full">
                                            <x-input
                                                placeholder="Fotos por pliego"
                                                class="rounded-3xl"
                                                wire:model="form.group_by"
                                                type="number"
                                                min="1"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-tab>
                    @endif

                    @if($this->form->type !== \App\Enums\ProductType::ALBUM && $this->form->has_sizes)
                        <x-tab name="sizes" label="Tamaños" icon="s-window">
                            <div class="space-y-5">
                                {{-- @if($this->form->type === \App\Enums\ProductType::PRINT)
                                    <x-select
                                        :options="$this->sizesOptions"
                                        placeholder="Tamaños predefinidos"
                                        wire:model="select_size"
                                        wire:change="addSizeFromOptions($event.target.value)"
                                        class="rounded-3xl"
                                    />
                                @endif --}}
                                <x-button
                                    label="Añadir tamaño"
                                    icon="s-plus"
                                    class="btn-primary btn-outline rounded-3xl !uppercase"
                                    wire:click="addSize"
                                />

                                @foreach($sizes as $size)
                                    <div class="grid grid-cols-3 gap-5 p-5 shadow-lg rounded-3xl">
                                        <div class="col-span-full grid grid-cols-2 gap-5">
                                            <div class="col-span-1">
                                                <x-input
                                                    placeholder="Ancho"
                                                    class="rounded-3xl"
                                                    wire:model="sizes.{{ $loop->index }}.width"
                                                    required
                                                />
                                            </div>
                                            <div class="col-span-1">
                                                <x-input
                                                    placeholder="Altura"
                                                    class="rounded-3xl"
                                                    wire:model="sizes.{{ $loop->index }}.height"
                                                    required
                                                />
                                            </div>
                                        </div>

                                        <div class="col-span-1">
                                            <div>
                                                <label class="input input-bordered input-primary rounded-3xl flex items-center justify-between gap-2 w-full mt-1">
                                                    <input
                                                        placeholder="Precio"
                                                        class="w-full"
                                                        type="number"
                                                        wire:model="sizes.{{ $loop->index }}.price"
                                                        required
                                                    >
                                                    <span class="badge badge-lg">€</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-span-1">
                                            <x-input
                                                placeholder="Cantidad mínima"
                                                class="rounded-3xl"
                                                wire:model="sizes.{{ $loop->index }}.min_photos"
                                                required
                                            />
                                        </div>
                                        <div class="col-span-1">
                                            <x-input
                                                placeholder="Cantidad máxima"
                                                class="rounded-3xl"
                                                wire:model="sizes.{{ $loop->index }}.max_photos"
                                                required
                                            />
                                        </div>
                                        <div class="col-span-full">
                                            <x-button
                                                icon="s-trash"
                                                class="btn-accent btn-outline btn-circle float-end btn-sm"
                                                wire:click="removeSize({{ $loop->index }})"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-tab>
                    @endif
                </x-tabs>
            </x-form>
        </div>
    </x-drawer>
</div>
