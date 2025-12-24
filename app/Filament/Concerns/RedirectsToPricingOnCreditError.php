<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\ValidationException;

trait RedirectsToPricingOnCreditError
{
    protected function redirectToPricingForCredits(ValidationException $exception): void
    {
        $message = collect($exception->errors())->flatten()->first()
            ?? 'Listing credits are required to publish this property.';

        Notification::make()
            ->title('Listing credits required')
            ->body($message)
            ->warning()
            ->send();

        $this->redirectRoute('pricing', [], true, true);

        throw (new Halt())->rollBackDatabaseTransaction();
    }
}
