<?php

namespace App\Models;

use LucasDotVin\Soulbscription\Models\Plan as LucasPlan;

class Plan extends LucasPlan
{
    const BASIC = 1;

    protected $fillable = [
        'id',
        'name',
        'description',
        'periodicity_type',
        'periodicity',
        'grace_days',
    ];
}
