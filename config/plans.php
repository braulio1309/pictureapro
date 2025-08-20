<?php
return [
    'plans' => [
        [
            'name' => 'Básico',
            'price_id' => env('STRIPE_PRICE_BASIC'),
            'features' => ['X usuarios', 'Soporte email'],
        ],
        [
            'name' => 'Pro',
            'price_id' => env('STRIPE_PRICE_PRO'),
            'features' => ['Todo del Básico', 'Soporte prioritario'],
        ],
    ],
];
