<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarQuestion extends Model
{
    protected $fillable = [
        'calendar_id',
        'question',
        'is_active',
        'position',
        'is_required',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'calendar_question_services');
    }
}
