<div>
    <x-header title="Mi cuenta" separator progress-indicator />

    <livewire:components.users.setting-tabs />

    <x-form wire:submit="update" class="w-full mt-5 gap-5">

        <div class="grid md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <x-input
                    label="Razón social"
                    class="rounded-3xl"
                    wire:model="form.company_name"
                    inline
                />
            </div>
            <div class="col-span-1">
                <x-input
                    label="NIF / DNI"
                    class="rounded-3xl"
                    wire:model="form.nif_document"
                    inline
                />
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
            <div class="col-span-1">
                <x-input
                    label="Código Postal"
                    class="rounded-3xl"
                    wire:model="form.postal_code"
                    inline
                />
            </div>
            <div class="col-span-1">
                <x-input
                    label="Dirección"
                    class="rounded-3xl"
                    wire:model="form.address"
                    inline
                />
            </div>
            <div class="col-span-1">
                <x-input
                    label="Teléfono"
                    class="rounded-3xl"
                    wire:model="form.phone_number"
                    inline
                />
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
            <div class="col-span-1">
                <x-input
                    label="Ciudad"
                    class="rounded-3xl"
                    wire:model="form.city_name"
                    inline
                />
            </div>
            <div class="col-span-1">
                <x-input
                    label="Provincia"
                    class="rounded-3xl"
                    wire:model="form.province_name"
                    inline
                />
            </div>
            <div class="col-span-1">
                <x-input
                    label="País"
                    class="rounded-3xl"
                    wire:model="form.country_name"
                    inline
                />
            </div>
        </div>

        <div class="mt-3">
            <x-file
                label="Firma"
                change-text="Seleccionar imagen"
                wire:model="signature"
                accept="image/png, image/jpeg"
            >
                <img src="{{ $this->form->user->getFirstMediaUrlCustom('signature') ?? asset('images/placeholder.webp') }}" class="h-32 rounded-lg" />
            </x-file>
            @if(!is_null($this->form->user->getFirstMediaUrlCustom('signature')))
                <x-button
                    label="Eliminar firma"
                    wire:click="removeSignature"
                    class="btn-sm btn-accent text-white mt-3"
                />
            @endif
        </div>

        <x-slot:actions>
            <x-button
                label="Guardar"
                type="submit"
                icon="o-check"
                class="btn-primary rounded-3xl !uppercase"
                spinner="save"
            />
        </x-slot:actions>

    </x-form>
</div>
