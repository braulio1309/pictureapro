<?php

namespace Database\Seeders;

use App\Jobs\Users\AssignRoleToUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Administrador',
                'lastname' => '',
                'email' => 'admin@crmfotografia.test',
                'password' => bcrypt('p4ssw0rd'),
                'is_enabled' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($data as $admin) {
            $user = User::create($admin);

            dispatch(new AssignRoleToUser($user->id, Role::ADMIN));
        }
    }
}
