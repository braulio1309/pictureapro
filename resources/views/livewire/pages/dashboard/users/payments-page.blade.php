<div>
    <x-header title="Mi cuenta" separator progress-indicator />

    <livewire:components.users.setting-tabs />

    <x-form wire:submit="update" class="w-full mt-5 gap-8">
        <div class="space-y-3">
            <h2 class="text-3xl font-medium">Transferencia / Bizum</h2>

            <x-editor
                wire:model="form.transfer_details"
                class="rounded-3xl"
            />
        </div>

        <div class="space-y-3">
            <h2 class="text-3xl font-medium">Stripe</h2>

            <p>Obtené las credenciales accediendo a tu <a href="https://dashboard.stripe.com/" target="_blank" class="text-accent underline">panel de Stripe</a>.</p>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="md:col-span-1">
                    <x-input
                        label="Publishable Key"
                        class="rounded-3xl"
                        wire:model="form.stripe_pub"
                        inline
                    />
                </div>
                <div class="col-span-1">
                    <x-input
                        label="Private Key"
                        class="rounded-3xl"
                        wire:model="form.stripe_priv"
                        inline
                    />
                </div>
            </div>
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
