<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: string implements HasLabel
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case SOLD = 'sold';
    case UNDER_OFFER = 'under_offer';
    case WITHDRAWN = 'withdrawn';
    case PENDING = 'pending';
    case INACTIVE = 'inactive';
    case OFF_MARKET = 'off_market';

    public function getLabel(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::RENTED => 'Rented',
            self::SOLD => 'Sold',
            self::UNDER_OFFER => 'Under Offer',
            self::WITHDRAWN => 'Withdrawn',
            self::PENDING => 'Pending',
            self::INACTIVE => 'Inactive',
            self::OFF_MARKET => 'Off Market',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::RENTED => 'warning',
            self::SOLD => 'danger',
            self::UNDER_OFFER => 'info',
            self::WITHDRAWN => 'gray',
            self::PENDING => 'primary',
            self::INACTIVE => 'gray',
            self::OFF_MARKET => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AVAILABLE => 'heroicon-o-check-circle',
            self::RENTED => 'heroicon-o-home',
            self::SOLD => 'heroicon-o-banknotes',
            self::UNDER_OFFER => 'heroicon-o-hand-raised',
            self::WITHDRAWN => 'heroicon-o-x-circle',
            self::PENDING => 'heroicon-o-clock',
            self::INACTIVE => 'heroicon-o-pause-circle',
            self::OFF_MARKET => 'heroicon-o-eye-slash',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->getLabel()])
            ->all();
    }
}
