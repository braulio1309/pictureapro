<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. read provinces from csv file
        $file = fopen(storage_path('/seeders/spain_provinces.csv'), 'r');
        $data = [];
        while (($line = fgetcsv($file)) !== false) {
            $data[] = [
                'country_code' => $line[0],
                'name' => $line[1],
            ];
        }
        fclose($file);
        $data = array_slice($data, 1);

        // 2. get all countries to search by code later
        $countries = Country::select('id', 'code')->get();

        // 2. insert by upsert
        foreach($data as $province) {
            $country = $countries->firstWhere('code', $province['country_code']);

            Province::updateOrCreate([
                'name' => $province['name'],
                'country_id' => $country->id,
            ]);
        }
    }
}
