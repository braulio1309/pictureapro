<?php

namespace App\Livewire\Forms;

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;
    public string $name = '';
    public bool $is_active = true;
    public ?ProductType $type = ProductType::ALBUM;
    public string $description = '';
    public ?float $price = null;
    public ?int $min_photos = null;
    public ?int $max_photos = null;
    public ?int $min_pages = null;
    public ?int $max_pages = null;
    public ?float $page_price = null;
    public ?int $group_by = null;
    public bool $has_sizes = true;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'type' => ['required', Rule::enum(ProductType::class)],
            'description' => ['nullable', 'string'],
            'price' => ['required_if_declined:has_sizes', 'nullable', 'numeric', 'min:1'],
            'min_photos' => ['nullable', 'integer', 'min:1'],
            'max_photos' => ['nullable', 'integer', 'min:1'],
            'min_pages' => ['nullable', 'integer', 'min:1'],
            'max_pages' => ['nullable', 'integer', 'min:1'],
            'page_price' => ['nullable', 'numeric', 'min:1'],
            'group_by' => ['nullable', 'integer', 'min:1'],
            'has_sizes' => ['boolean'],
        ];
    }
    public function store(): Product
    {
        $data = $this->validate();
        $product = Product::create($data);

        $this->reset();

        return $product;
    }

    public function set(Product $product): void
    {
        $this->product = $product;

        $this->name = $product->name;
        $this->is_active = $product->is_active;
        $this->type = $product->type;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->min_photos = $product->min_photos;
        $this->max_photos = $product->max_photos;
        $this->min_pages = $product->min_pages;
        $this->max_pages = $product->max_pages;
        $this->page_price = $product->page_price;
        $this->group_by = $product->group_by;
        $this->has_sizes = $product->has_sizes;
    }

    public function update(): Product
    {
        $data = $this->validate();
        $this->product->update($data);

        return $this->product;
    }

    public function delete(): bool
    {
        $this->product->media()->delete();
        $this->product->sizes()->delete();

        return $this->product->delete();
    }
}
