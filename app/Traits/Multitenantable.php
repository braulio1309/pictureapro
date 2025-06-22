<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait Multitenantable
{
    protected static function bootMultitenantable()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                return;
            }

            static::creating(function ($model) use ($user) {
                $model->tenant_id = $user->id;
            });

            static::addGlobalScope('tenant_id', function (Builder $builder) use ($user) {
                $builder->where('tenant_id', $user->id);
            });
        }
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}