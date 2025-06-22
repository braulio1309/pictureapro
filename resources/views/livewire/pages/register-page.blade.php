<div class="w-full">
    <h1 class="text-2xl font-semibold uppercase">Crear cuenta</h1>

    <x-form wire:submit="register" class="w-full mt-5">
        <x-input
            placeholder="Nombre"
            class="rounded-3xl"
            icon="s-user"
            wire:model="form.name"
            required
        />
        <x-input
            placeholder="Apellido"
            class="rounded-3xl"
            icon="s-user"
            wire:model="form.lastname"
            required
        />
        <x-input
            placeholder="Correo"
            class="rounded-3xl"
            icon="s-envelope"
            type="email"
            wire:model="form.email"
            required
        />
        <x-input
            placeholder="Contraseña"
            class="rounded-3xl"
            icon="s-key"
            type="password"
            wire:model="form.password"
            required
        />

        <x-slot:actions>
            <x-button
                label="Ingresar"
                class="btn-link rounded-3xl !uppercase"
                wire-navigate
                link="{{ route('login') }}"
            />

            <x-button
                label="Comenzar"
                type="submit"
                class="btn-primary rounded-3xl !uppercase"
                spinner="register"
            />
        </x-slot:actions>
    </x-form>
</div>