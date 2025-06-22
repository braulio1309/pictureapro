<x-mail::message>
# Reserva confirmada

<x-mail::table>
| Información       |                                 |
|-------------------|--------------------------------:|
| **Fotógrafo**     | {{ $data['photographer_name']}} |
| **Paquete**       | {{ $data['service_name']}}      |
@if(!is_null($data['date']))
| **Fecha**         | {{ $data['date']}}              |
@endif
| **Valor reserva** | €{{ $data['price']}}            |
| **Método pago**   | {{ $data['payment_method'] }}   |
</x-mail::table>

</x-mail::message>
