<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class UserObserver
{
    public function created(User $user): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $admins = User::query()
            ->whereIn('user_type', ['admin', 'super_admin'])
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $contact = $user->email ?: $user->phone ?: 'No contact';

        $typeConfig = $this->notificationConfigFor($user);

        Notification::make()
            ->title($typeConfig['title'])
            ->body(sprintf($typeConfig['body'], $user->name, $contact, $typeConfig['label']))
            ->icon($typeConfig['icon'])
            ->iconColor($typeConfig['color'])
            ->actions([
                Action::make('view')
                    ->label('View user')
                    ->button()
                    ->url(route('filament.admin.resources.users.view', $user)),
            ])
            ->sendToDatabase($admins, isEventDispatched: true);
    }

    private function notificationConfigFor(User $user): array
    {
        return match ($user->user_type) {
            'agent' => [
                'title' => 'New agent registered',
                'body' => '%s (%s) joined as %s.',
                'label' => 'Agent',
                'icon' => 'heroicon-o-briefcase',
                'color' => 'info',
            ],
            'agency_owner' => [
                'title' => 'New agency owner registered',
                'body' => '%s (%s) registered as %s.',
                'label' => 'Agency Owner',
                'icon' => 'heroicon-o-building-office',
                'color' => 'primary',
            ],
            'property_owner' => [
                'title' => 'New landlord registered',
                'body' => '%s (%s) registered as %s.',
                'label' => 'Landlord',
                'icon' => 'heroicon-o-home',
                'color' => 'warning',
            ],
            'tenant' => [
                'title' => 'New tenant registered',
                'body' => '%s (%s) joined as %s.',
                'label' => 'Tenant',
                'icon' => 'heroicon-o-key',
                'color' => 'success',
            ],
            'customer' => [
                'title' => 'New customer registered',
                'body' => '%s (%s) joined as %s.',
                'label' => 'Customer',
                'icon' => 'heroicon-o-user',
                'color' => 'info',
            ],
            'admin', 'super_admin' => [
                'title' => 'New admin user created',
                'body' => '%s (%s) registered as %s.',
                'label' => 'Administrator',
                'icon' => 'heroicon-o-shield-check',
                'color' => 'success',
            ],
            default => [
                'title' => 'New user registration',
                'body' => '%s (%s) registered as %s.',
                'label' => 'User',
                'icon' => 'heroicon-o-user-plus',
                'color' => 'success',
            ],
        };
    }
}
