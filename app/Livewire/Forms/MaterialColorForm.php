<?php

namespace App\Livewire\Forms;

use App\Models\MaterialColor;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MaterialColorForm extends Form
{
    public ?MaterialColor $color = null;
    public int $material_id = 0;
    public string $name = '';
    public bool $is_active = true;

    public function rules()
    {
        return [
            'material_id' => ['required', 'integer', 'exists:App\Models\Material,id'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
    public function store(): MaterialColor
    {
        $data = $this->validate();
        $color = MaterialColor::create($data);

        $this->reset();

        return $color;
    }

    public function set(MaterialColor $color): void
    {
        $this->color = $color;

        $this->material_id = $color->material_id;
        $this->name = $color->name;
        $this->is_active = $color->is_active;
    }

    public function update(): MaterialColor
    {
        $data = $this->validate();
        $this->color->update($data);

        return $this->color;
    }

    public function delete(): bool
    {
        return $this->color->delete();
    }
}
