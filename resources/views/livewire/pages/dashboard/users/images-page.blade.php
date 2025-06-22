<div>
    <x-header title="Mi cuenta" separator progress-indicator />

    <livewire:components.users.setting-tabs />

    <x-form wire:submit="update" class="w-full mt-5">
        <div class="grid md:grid-cols-3 gap-5">
            <div class="col-span-1 bg-base-200 rounded-xl p-5 h-max">
                <x-file
                    label="Tu logo"
                    wire:model="logo"
                    accept="image/png, image/jpeg"
                    change-text="Seleccionar imagen"
                    wire:loading.attr="disabled"
                    wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                >
                    <img
                        class="w-full rounded-lg"
                        src="{{ $this->form->user->getFirstMediaUrlCustom('logo') ?? asset('images/placeholder.webp') }}"
                    />
                </x-file>
                @if(!is_null($this->form->user->getFirstMediaUrlCustom('logo')))
                    <x-button
                        label="Eliminar"
                        wire:click="removeFile('logo')"
                        class="btn-sm btn-accent text-white mt-3"
                        wire:loading.attr="disabled"
                        wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                    />
                @endif
            </div>

            <div class="col-span-1 bg-base-200 rounded-xl p-5 h-max">
                <x-file
                    label="Marca de agua horizontal"
                    wire:model="watermark_horizontal"
                    accept="image/png, image/jpeg"
                    change-text="Seleccionar imagen"
                    wire:loading.attr="disabled"
                    wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                >
                    <img
                        class="w-full rounded-lg"
                        src="{{ $this->form->user->getFirstMediaUrlCustom('watermark_horizontal') ?? asset('images/placeholder.webp') }}"
                    />
                </x-file>
                @if(!is_null($this->form->user->getFirstMediaUrlCustom('watermark_horizontal')))
                    <x-button
                        label="Eliminar"
                        wire:click="removeFile('watermark_horizontal')"
                        class="btn-sm btn-accent text-white mt-3"
                        wire:loading.attr="disabled"
                        wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                    />
                @endif
            </div>

            <div class="col-span-1 bg-base-200 rounded-xl p-5 h-max">
                <x-file
                    label="Marca de agua vertical"
                    wire:model="watermark_vertical"
                    accept="image/png, image/jpeg"
                    change-text="Seleccionar imagen"
                    wire:loading.attr="disabled"
                    wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                >
                    <img
                        class="w-full rounded-lg"
                        src="{{ $this->form->user->getFirstMediaUrlCustom('watermark_vertical') ?? asset('images/placeholder.webp') }}"
                    />
                </x-file>
                @if(!is_null($this->form->user->getFirstMediaUrlCustom('watermark_vertical')))
                    <x-button
                        label="Eliminar"
                        wire:click="removeFile('watermark_vertical')"
                        class="btn-sm btn-accent text-white mt-3"
                        wire:loading.attr="disabled"
                        wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                    />
                @endif
            </div>
        </div>

        <x-slot:actions>
            <div class="flex items-center space-x-4">
                <p class="italic text-primary" wire:loading>Procesando imagen..</p>
                <x-button
                    label="Guardar"
                    type="submit"
                    icon="o-check"
                    class="btn-primary rounded-3xl !uppercase"
                    spinner="save"
                    wire:loading.attr="disabled"
                    wire:target="logo,watermark_horizontal,watermark_vertical,removeFile,submit"
                />
            </div>
        </x-slot:actions>

    </x-form>
</div>
