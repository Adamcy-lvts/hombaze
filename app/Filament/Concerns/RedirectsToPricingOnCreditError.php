<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\ValidationException;

trait RedirectsToPricingOnCreditError
{
    protected function redirectToPricingForCredits(ValidationException $exception): void
    {
        $message = collect($exception->errors())->flatten()->first()
            ?? 'Insufficient credits to publish this property.';

        Notification::make()
            ->title('Insufficient credits')
            ->body($message)
            ->actions([
                Action::make('viewPricing')
                    ->label('View pricing')
                    ->url(route('pricing')),
            ])
            ->warning()
            ->send();

        throw (new Halt())->rollBackDatabaseTransaction();
    }
}
