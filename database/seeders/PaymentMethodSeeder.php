<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Transferencia / Bizum',
                'description' => null,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Pago con tarjeta',
                'description' => null,
                'is_active' => false,
            ],
        ];

        PaymentMethod::upsert($data, uniqueBy: ['id'], update: ['name', 'description', 'is_active']);
    }
}
