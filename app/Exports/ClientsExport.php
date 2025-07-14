<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Client::where('tenant_id', '=', Auth::user()->id)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellidos',
            'Email',
            'Telefono',
            'NIF',
            'Direccion',
            'Codigo postal',
            'Ciudad',
            'Provincia',
            'Pais',
            'Newsletter',
            'Publicar',
            'Notas',
            'Fecha de Creacion',
            'Última Actualizacion'
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name ,
            $client->lastname,
            $client->email,
            $client->phone_number,
            $client->nif_document,
            $client->address,
            $client->postal_code,
            $client->city_name,
            $client->province_name,
            $client->country_name,
            ($client->allow_commercial_comms)? 'Permite': 'No permite',
            ($client->allow_publish_images)? 'Permite': 'No permite',
            $client->note,
            $client->created_at->format('d/m/Y H:i'),
            $client->updated_at->format('d/m/Y H:i')
        ];
    }
}