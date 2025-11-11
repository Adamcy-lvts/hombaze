<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: string implements HasLabel
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case SOLD = 'sold';
    case OFF_MARKET = 'off_market';

    public function getLabel(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::RENTED => 'Rented',
            self::SOLD => 'Sold',
            self::OFF_MARKET => 'Off Market',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::RENTED => 'warning',
            self::SOLD => 'danger',
            self::OFF_MARKET => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AVAILABLE => 'heroicon-o-check-circle',
            self::RENTED => 'heroicon-o-home',
            self::SOLD => 'heroicon-o-banknotes',
            self::OFF_MARKET => 'heroicon-o-eye-slash',
        };
    }
}