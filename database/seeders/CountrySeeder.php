<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'España',
                'code' => 'es'
            ],
        ];

        foreach($data as $country) {
            Country::updateOrCreate([
                'name' => $country['name'],
                'code' => $country['code'],
            ]);
        }
    }
}
