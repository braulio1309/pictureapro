<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Client;

class ClientForm extends Form
{
    public ?Client $client = null;
    public string $name = '';
    public string $lastname = '';
    public string $email = '';
    public ?string $nif_document = null;
    public ?string $phone_number = null;
    public ?string $address = null;
    public ?string $postal_code = null;
    public ?int $country_id = null;
    public ?string $country_name = null;
    public ?int $province_id = null;
    public ?string $province_name = null;
    public ?int $city_id = null;
    public ?string $city_name = null;
    public ?string $notes = null;
    public ?int $tenant_id = null;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'nif_document' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:15'],
            'country_id' => ['nullable', 'integer', 'exists:\App\Models\Country,id'],
            'country_name' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:\App\Models\Province,id'],
            'province_name' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'integer', 'exists:\App\Models\City,id'],
            'city_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'tenant_id' => ['nullable', 'integer', 'exists:\App\Models\User,id'],
        ];
    }
    public function store(): Client
    {
        $data = $this->validate();
        $client = Client::create($data);

        $this->reset();

        return $client;
    }

    public function set(Client $client): void
    {
        $this->client = $client;

        $this->name = $client->name;
        $this->lastname = $client->lastname;
        $this->email = $client->email;
        $this->nif_document = $client->nif_document;
        $this->phone_number = $client->phone_number;
        $this->address = $client->address;
        $this->postal_code = $client->postal_code;
        $this->country_id = $client->country_id;
        $this->country_name = $client->country_name;
        $this->province_id = $client->province_id;
        $this->province_name = $client->province_name;
        $this->city_id = $client->city_id;
        $this->city_name = $client->city_name;
        $this->notes = $client->notes;
        $this->tenant_id = $client->tenant_id;
    }

    public function update(): Client
    {
        $data = $this->validate();
        $this->client->update($data);

        return $this->client;
    }

    public function delete(): bool
    {
        return $this->client->delete();
    }
}
