<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'admin',
                'description' => 'Administrador'
            ],
            [
                'id' => 2,
                'name' => 'photographer',
                'description' => 'Fotógrafo'
            ],
            [
                'id' => 3,
                'name' => 'client',
                'description' => 'Cliente'
            ],
        ];

        foreach ($data as $role) {
            Role::updateOrCreate($role);
        }
    }
}
