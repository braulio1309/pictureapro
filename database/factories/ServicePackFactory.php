<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicePack>
 */
class ServicePackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::query()->inRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'is_active' => $this->faker->boolean,
            'description' => $this->faker->text,
            'duration' => $this->faker->numberBetween(15, 60),
            'price' => $this->faker->randomFloat(2, 50, 100),
            'reservation_price' => $this->faker->randomFloat(2, 1, 50),
        ];
    }
}
