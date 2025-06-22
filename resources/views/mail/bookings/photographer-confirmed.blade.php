<x-mail::message>
# Reserva confirmada

<x-mail::table>
| Información       |                               |
|-------------------|------------------------------:|
| **Calendario**    | {{ $data['calendar_name']}}   |
| **Cliente**       | {{ $data['client_name']}}     |
| **Paquete**       | {{ $data['service_name']}}    |
@if(!is_null($data['date']))
| **Fecha**         | {{ $data['date']}}            |
@endif
| **Valor reserva** | €{{ $data['price']}}          |
| **Método pago**   | {{ $data['payment_method'] }} |
</x-mail::table>

<x-mail::button url="{{ $data['url'] }}">
Ver reserva
</x-mail::button>

</x-mail::message>
