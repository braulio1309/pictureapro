<?php

namespace App\Livewire\Forms;

use App\Models\Material;
use Livewire\Form;

class MaterialForm extends Form
{
    public ?Material $material = null;
    public string $name = '';
    public bool $is_active = true;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
    public function store(): Material
    {
        $data = $this->validate();
        $material = Material::create($data);

        $this->reset();

        return $material;
    }

    public function set(Material $material): void
    {
        $this->material = $material;

        $this->name = $material->name;
        $this->is_active = $material->is_active;
    }

    public function update(): Material
    {
        $data = $this->validate();
        $this->material->update($data);

        return $this->material;
    }

    public function delete(): bool
    {
        $this->material->colors()->delete();

        return $this->material->delete();
    }
}
