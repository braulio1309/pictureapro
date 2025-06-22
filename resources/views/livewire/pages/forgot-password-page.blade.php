<div class="w-full flex flex-col gap-5">
    <h1 class="text-2xl font-semibold uppercase">Recuperar contraseña</h1>

    @if(session('error'))
        <x-alert title="{{ session('error') }}" icon="o-exclamation-triangle" class="alert-error text-white" />
    @endif
    @if(session('success'))
        <x-alert title="{{ session('success') }}" icon="o-check" class="alert-success" />
    @endif

    <x-form wire:submit="sendResetEmail" class="w-full">
        <x-input
            placeholder="Correo"
            class="rounded-3xl"
            type="email"
            wire:model="email"
            icon="s-envelope"
            required
        />

        <x-slot:actions>
            <x-button
                label="Ingresar"
                class="btn-link !uppercase"
                wire-navigate
                link="{{ route('login') }}"
            />

            <x-button
                label="Enviar correo"
                type="submit"
                class="btn-primary rounded-3xl !uppercase"
                spinner="register"
            />
        </x-slot:actions>
    </x-form>
</div>