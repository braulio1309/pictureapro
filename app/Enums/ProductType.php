<?php

namespace App\Enums;

enum ProductType: string
{
    case ALBUM = 'album';
    case PRINT = 'print';
    case OTHER = 'other';
    case NO_PHOTO = 'no-photo';

    public static function toArray(): array
    {
        return [
            self::ALBUM->value,
            self::PRINT->value,
            self::OTHER->value,
            self::NO_PHOTO->value,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALBUM => 'Álbum',
            self::PRINT => 'Impresiones',
            self::OTHER => 'Otros',
            self::NO_PHOTO => 'Sin fotos',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ALBUM => 's-book-open',
            self::PRINT => 's-printer',
            self::OTHER => 'o-clipboard',
            self::NO_PHOTO => 's-no-symbol',
        };
    }

    public static function getOptions(): array
    {
        return [
            [
                'id' => self::ALBUM,
                'value' => self::ALBUM->getLabel(),
                'icon' => self::ALBUM->getIcon(),
            ],
            [
                'id' => self::PRINT,
                'value' => self::PRINT->getLabel(),
                'icon' => self::PRINT->getIcon(),
            ],
            [
                'id' => self::OTHER,
                'value' => self::OTHER->getLabel(),
                'icon' => self::OTHER->getIcon(),
            ],
            [
                'id' => self::NO_PHOTO,
                'value' => self::NO_PHOTO->getLabel(),
                'icon' => self::NO_PHOTO->getIcon(),
            ],
        ];
    }
}
