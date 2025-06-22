<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'basic',
                'description' => 'Básico',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity' => 1,
            ],
        ];

        foreach ($data as $plan) {
            Plan::updateOrCreate($plan);
        }
    }
}
