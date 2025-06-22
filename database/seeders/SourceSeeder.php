<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Recomendación',
            ],
            [
                'id' => 2,
                'name' => 'Redes sociales',
            ],
            [
                'id' => 3,
                'name' => 'Motores de Búsqueda (Google / Bing)',
            ],
        ];

        foreach ($data as $role) {
            Source::updateOrCreate($role);
        }
    }
}
