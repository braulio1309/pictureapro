<div class="w-full flex flex-col gap-5">
    <h1 class="text-2xl uppercase text-slate-800 font-bold mb-6">Verificar correo ✨</h1>

    <div class="flex flex-col gap-3">
        <p><span class="font-semibold">Muchas gracias por registrarte!</span> Antes de poder continuar debes verificar tu correo mediante el enlace que te enviamos al mismo.</p>
        <p>En caso de no haberlo recibido, revisa la carpeta de 'correo no deseado' o selecciona la opción de enviar uno nuevo.</p>
    </div>

    @if(session('success'))
        <x-alert icon="o-check" title="{{ session('success') }}" class="alert-success" />
    @endif

    <div class="mt-6 flex items-center justify-between">
        <x-button
            label="Cerrar sesión"
            class="btn-link !uppercase"
            no-wire-navigate
            link="{{ route('logout') }}"
        />
        <x-button
            label="Reenviar correo"
            class="btn-primary rounded-3xl !uppercase"
            wire:click="sendVerificationEmail"
            loading="sendVerificationEmail"
        />
    </div>
</div>