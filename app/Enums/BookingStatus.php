<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';

    public static function toArray(): array
    {
        return [
            self::PENDING->value,
            self::CONFIRMED->value,
            self::CANCELLED->value,
            self::COMPLETED->value,
            self::EXPIRED->value,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmada',
            self::CANCELLED => 'Cancelada',
            self::COMPLETED => 'Completada',
            self::EXPIRED => 'Expirada',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'o-clock',
            self::CONFIRMED => 'o-check',
            self::CANCELLED => 'o-x-mark',
            self::COMPLETED => 's-check-circle',
            self::EXPIRED => 'o-exclamation-triangle',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PENDING => 'accent text-white',
            self::CONFIRMED => 'primary',
            self::CANCELLED => 'accent badge-outline',
            self::COMPLETED => 'neutral',
            self::EXPIRED => 'accent badge-outline',
        };
    }

    public static function getOptions(): array
    {
        return [
            [
                'id' => self::PENDING,
                'value' => self::PENDING->getLabel(),
                'icon' => self::PENDING->getIcon(),
            ],
            [
                'id' => self::CONFIRMED,
                'value' => self::CONFIRMED->getLabel(),
                'icon' => self::CONFIRMED->getIcon(),
            ],
            [
                'id' => self::CANCELLED,
                'value' => self::CANCELLED->getLabel(),
                'icon' => self::CANCELLED->getIcon(),
            ],
            [
                'id' => self::COMPLETED,
                'value' => self::COMPLETED->getLabel(),
                'icon' => self::COMPLETED->getIcon(),
            ],
            [
                'id' => self::EXPIRED,
                'value' => self::EXPIRED->getLabel(),
                'icon' => self::EXPIRED->getIcon(),
            ],
        ];
    }
}
