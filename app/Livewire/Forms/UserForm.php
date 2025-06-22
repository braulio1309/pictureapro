<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;

class UserForm extends Form
{
    public ?User $user = null;
    public string $name = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public bool $is_enabled = true;
    public ?string $company_name = null;
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
    public ?string $timezone = null;
    public ?string $notes = null;
    public ?int $tenant_id = null;
    public ?int $role_id = null;
    public ?string $transfer_details = null;
    public ?string $stripe_pub = null;
    public ?string $stripe_priv = null;
    public ?string $stripe_wh_id = null;
    public ?string $stripe_wh_secret = null;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:\App\Models\User,email,' . optional($this->user)->id],
            'password' => ['sometimes', 'string', 'min:8'],
            'is_enabled' => ['required', 'boolean'],
            'company_name' => ['nullable', 'string', 'max:255'],
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
            'timezone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'tenant_id' => ['nullable', 'integer', 'exists:\App\Models\User,id'],
            'role_id' => ['nullable', 'integer', 'exists:\App\Models\Role,id'],
            'transfer_details' => ['nullable', 'string'],
            'stripe_pub' => ['nullable', 'string'],
            'stripe_priv' => ['nullable', 'string'],
            'stripe_wh_id' => ['nullable', 'string'],
            'stripe_wh_secret' => ['nullable', 'string'],
        ];
    }
    public function store(): User
    {
        $data = $this->validate();
        $data['password'] = bcrypt($this->password);
        $user = User::create($data);

        if (!is_null($this->role_id)) {
            $user->roles()->attach($this->role_id);
        }

        $this->reset();

        return $user;
    }

    public function set(User $user): void
    {
        $this->user = $user;

        $this->name = $user->name;
        $this->lastname = $user->lastname;
        $this->email = $user->email;
        $this->is_enabled = $user->is_enabled;
        $this->company_name = $user->company_name;
        $this->nif_document = $user->nif_document;
        $this->phone_number = $user->phone_number;
        $this->address = $user->address;
        $this->postal_code = $user->postal_code;
        $this->country_id = $user->country_id;
        $this->country_name = $user->country_name;
        $this->province_id = $user->province_id;
        $this->province_name = $user->province_name;
        $this->city_id = $user->city_id;
        $this->city_name = $user->city_name;
        $this->timezone = $user->timezone;
        $this->notes = $user->notes;
        $this->tenant_id = $user->tenant_id;
        $this->transfer_details = $user->transfer_details;
        $this->stripe_pub = $user->stripe_pub;
        $this->stripe_priv = $user->stripe_priv;
        $this->stripe_wh_id = $user->stripe_wh_id;
        $this->stripe_wh_secret = $user->stripe_wh_secret;
    }

    public function update(): User
    {
        $data = $this->validate();
        unset($data['password']);

        if (empty($data['stripe_priv'])) {
            $data['stripe_wh_id'] = null;
            $data['stripe_wh_secret'] = null;
        }

        $this->user->update($data);

        return $this->user;
    }

    public function delete(): bool
    {
        return $this->user->delete();
    }
}
