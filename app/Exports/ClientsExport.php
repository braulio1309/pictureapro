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
            'Nombre Completo',
            'Email',
            'Telefono',
            'DNI',
            'Pais',
            'Ciudad',
            'Direccion',
            'Codigo postal',
            'Notas',
            'Fecha de Creacion',
            'Última Actualizacion'
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->name . ' ' .$client->lastname,
            $client->email,
            $client->phone_number,
            $client->nif_document,
            $client->nif_document,
            $client->country_name,
            $client->city_name,
            $client->address,
            $client->postal_code,
            $client->note,
            $client->created_at->format('d/m/Y H:i'),
            $client->updated_at->format('d/m/Y H:i')
        ];
    }
}