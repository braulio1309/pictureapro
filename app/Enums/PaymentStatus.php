<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public static function toArray(): array
    {
        return [
            self::PENDING->value,
            self::COMPLETED->value,
            self::FAILED->value,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Expirada',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'o-clock',
            self::COMPLETED => 'o-check-',
            self::FAILED => 'o-exclamation-triangle',
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
                'id' => self::COMPLETED,
                'value' => self::COMPLETED->getLabel(),
                'icon' => self::COMPLETED->getIcon(),
            ],
            [
                'id' => self::FAILED,
                'value' => self::FAILED->getLabel(),
                'icon' => self::FAILED->getIcon(),
            ],
        ];
    }
}
