<div class="w-full flex flex-col gap-5">
    <h1 class="text-2xl font-semibold uppercase">Iniciar sesión</h1>

    @if(session('error'))
        <x-alert title="{{ session('error') }}" icon="o-exclamation-triangle" class="alert-error text-white" />
    @endif
    @if(session('success'))
        <x-alert title="{{ session('success') }}" icon="o-check" class="alert-success" />
    @endif

    <x-form wire:submit="login" class="w-full text-right">
        <x-input
            type="email"
            placeholder="Correo electrónico"
            class="rounded-3xl"
            wire:model="email"
            icon="s-envelope"
            required
        />

        <x-input
            :type="$this->passwordType"
            placeholder="Contraseña"
            class="rounded-3xl"
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

        <a
            href="{{ route('password.request') }}"
            class="text-right text-sm text-primary mt-1"
            wire-navigate
        >Olvidé mi contraseña</a>

        <x-slot:actions>
            <x-button
                label="Crear cuenta"
                class="btn-primary btn-outline rounded-3xl !uppercase"
                wire-navigate
                link="{{ route('register') }}"
            />

            <x-button
                label="Acceder"
                type="submit"
                class="btn-primary rounded-3xl !uppercase"
                spinner="login"
            />
        </x-slot:actions>
    </x-form>
</div>