<?php

namespace App\Filament\Agent\Resources;

use App\Filament\Agent\Resources\TenantInvitationResource\Pages;
use App\Models\TenantInvitation;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class TenantInvitationResource extends Resource
{
    protected static ?string $model = TenantInvitation::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Tenant Invitations';
    protected static ?string $modelLabel = 'Tenant Invitation';
    protected static ?string $pluralModelLabel = 'Tenant Invitations';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invitation Details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Tenant Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->helperText('Enter the email address of the person you want to invite as a tenant'),

                        Forms\Components\Select::make('property_id')
                            ->label('Property (Optional)')
                            ->options(function () {
                                $user = Auth::user();
                                $agentProfile = $user->agentProfile;
                                
                                if (!$agentProfile) {
                                    return [];
                                }
                                
                                return Property::where('agent_id', $agentProfile->id)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->helperText('Select a specific property for this invitation (optional)'),

                        Forms\Components\Textarea::make('message')
                            ->label('Personal Message')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Add a personal message to include in the invitation email (optional)'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiry Date')
                            ->required()
                            ->default(now()->addDays(7))
                            ->minDate(now())
                            ->helperText('When should this invitation expire?'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Tenant Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('Any Property'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'expired',
                        'secondary' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'accepted',
                        'heroicon-o-x-circle' => 'expired',
                        'heroicon-o-minus-circle' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Accepted')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Not accepted')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenantUser.name')
                    ->label('Tenant Name')
                    ->placeholder('Not registered')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->options(function () {
                        $user = Auth::user();
                        $agentProfile = $user->agentProfile;
                        return $agentProfile ? Property::where('agent_id', $agentProfile->id)->pluck('title', 'id') : [];
                    })
                    ->placeholder('All Properties'),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_link')
                    ->label('Copy Link')
                    ->icon('heroicon-o-link')
                    ->action(function (TenantInvitation $record) {
                        // This would typically copy to clipboard via JavaScript
                        Notification::make()
                            ->title('Invitation Link')
                            ->body($record->getInvitationUrl())
                            ->success()
                            ->send();
                    })
                    ->visible(fn (TenantInvitation $record) => $record->isPending()),

                Tables\Actions\Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (TenantInvitation $record) {
                        // Update expiry date
                        $record->update(['expires_at' => now()->addDays(7)]);
                        
                        // TODO: Send email notification
                        
                        Notification::make()
                            ->title('Invitation Resent')
                            ->body('The invitation has been resent to ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->visible(fn (TenantInvitation $record) => $record->isPending()),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TenantInvitation $record) {
                        $record->markAsCancelled();
                        
                        Notification::make()
                            ->title('Invitation Cancelled')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (TenantInvitation $record) => $record->isPending()),

                Tables\Actions\EditAction::make()
                    ->visible(fn (TenantInvitation $record) => $record->isPending()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $agentProfile = $user->agentProfile;
        
        if (!$agentProfile) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }
        
        return parent::getEloquentQuery()
            ->where('agent_id', $agentProfile->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenantInvitations::route('/'),
            'create' => Pages\CreateTenantInvitation::route('/create'),
            'edit' => Pages\EditTenantInvitation::route('/{record}/edit'),
        ];
    }
}
