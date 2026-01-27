<?php

namespace App\Filament\Landlord\Resources\TenantResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Landlord\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    public function mount(int | string $record): void
    {
        \Log::info('[EditTenant] mount called', [
            'record_param' => $record,
            'auth_user_id' => Auth::id(),
        ]);

        parent::mount($record);

        \Log::info('[EditTenant] after parent mount', [
            'record_id' => $this->record?->id ?? 'null',
            'record_landlord_id' => $this->record?->landlord_id ?? 'null',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
