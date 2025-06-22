<x-mail::message>
# Hola {{ $name }}!

Bienvenido a {{ config('app.name') }}. Estamos muy contentos de tenerte con nosotros.

Si tienes alguna pregunta, no dudes en ponerte en contacto con nosotros.

<x-mail::button url="{{ config('app.url') }}">
Ir al sitio
</x-mail::button>

</x-mail::message>
