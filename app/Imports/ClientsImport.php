<?php
namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\Importable;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;



    public function model(array $row)
    {
        // Debug: Verifica los datos que llegan
        return new Client([
            'tenant_id' => Auth::user()->id,
            'name' => $row['nombre'],
            'lastname' => $row['apellidos'] ?? '',
            'email' => $row['email'],
            'phone_number' => strval($this->getValue($row, ['phone_number', 'phone', 'telefono'])),
            'address' => $this->getValue($row, ['direccion', 'address']),
            'nif_document' => $this->getValue($row, ['nif_document', 'nif', 'NIF']),
            'country_name' => $this->getValue($row, ['country_name', 'pais']),
            'city_name' => $this->getValue($row, ['city_name', 'ciudad']),
            'postal_code' => $this->getValue($row, ['postal_code', 'codigo_postal', 'codigo postal']),
            'allow_publish_images' => (intval($row['publicar']) == 1)? True: False,
            'allow_commercial_comms' => (intval($row['newsletter']) == 1)? True: False,
            'notes' => $this->getValue($row, ['notes', 'notas']),
            'province_name' => $this->getValue($row, ['provincia']),

        ]);
    }

    protected function getValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key])) {
                return $row[$key] !== '' ? $row[$key] : null;
            }
        }
        return null;
    }

    public function rules(): array
    {
        return [
           
           
        ];
    }
}