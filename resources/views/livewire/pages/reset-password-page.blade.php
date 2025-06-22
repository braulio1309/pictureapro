<div class="w-full flex flex-col gap-5">
    <h1 class="text-2xl font-semibold uppercase">Restablecer contraseña</h1>

    @if(session('error'))
        <x-alert title="{{ session('error') }}" icon="o-exclamation-triangle" class="alert-error text-white" />
    @endif
    @if(session('success'))
        <x-alert title="{{ session('success') }}" icon="o-check" class="alert-success" />
    @endif

    <x-form wire:submit="resetPassword" class="w-full">
        <x-input
            placeholder="Correo"
            class="rounded-3xl"
            type="email"
            wire:model="email"
            icon="s-envelope"
            required
            readonly
        />
        <x-input
            placeholder="Contraseña"
            class="rounded-3xl"
            :type="$this->passwordType"
            wire:model="password"
            icon="s-key"
            required
        >
            <x-slot:append>
                <x-button
                    :icon="$this->passwordIcon"
                    class="btn-primary border-l-0 bg-white btn-outline rounded-3xl rounded-s-none"
                    wire:click="togglePasswordVisibility"
                />
            </x-slot:append>
        </x-input>
        <x-input
            placeholder="Confirmar contraseña"
            class="rounded-3xl"
            :type="$this->passwordType"
            wire:model="password_confirmation"
            icon="s-key"
            required
        />

        <x-slot:actions>
            <x-button
                label="Restablecer"
                type="submit"
                class="btn-primary rounded-3xl !uppercase"
                spinner="register"
            />
        </x-slot:actions>
    </x-form>
</div>