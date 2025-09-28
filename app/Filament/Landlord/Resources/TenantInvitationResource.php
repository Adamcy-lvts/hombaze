<?php

namespace App\Filament\Landlord\Resources;

use App\Filament\Landlord\Resources\TenantInvitationResource\Pages;
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
                        Forms\Components\TextInput::make('phone')
                            ->label('Tenant Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+234 801 234 5678')
                            ->helperText('Enter the phone number of the person you want to invite as a tenant')
                            ->rules(['regex:/^(\+234|234|0)[789][01]\d{8}$/']),

                        Forms\Components\Select::make('property_id')
                            ->label('Property (Optional)')
                            ->options(function () {
                                $user = Auth::user();
                                $propertyOwner = $user->propertyOwnerProfile;
                                
                                if (!$propertyOwner) {
                                    return [];
                                }
                                
                                return Property::where('owner_id', $propertyOwner->id)
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
                Tables\Columns\TextColumn::make('phone')
                    ->label('Tenant Phone')
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
                    ->options(fn () => Property::where('owner_id', Auth::id())->pluck('title', 'id'))
                    ->placeholder('All Properties'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('copy_link')
                        ->label('Copy Link')
                        ->icon('heroicon-o-link')
                        ->action(function (TenantInvitation $record) {
                            $record->update([
                                'link_copied_at' => now(),
                                'link_copy_count' => $record->link_copy_count + 1
                            ]);

                            Notification::make()
                                ->title('Invitation Link Ready')
                                ->body('Click the link below to copy: ' . $record->getInvitationUrl())
                                ->success()
                                ->persistent()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('copy')
                                        ->label('Copy to Clipboard')
                                        ->url('javascript:copyInvitationLink("' . $record->getInvitationUrl() . '")')
                                        ->openUrlInNewTab(false)
                                ])
                                ->send();
                        })
                        ->visible(fn (TenantInvitation $record) => $record->isPending()),

                    Tables\Actions\Action::make('whatsapp')
                        ->label('Share via WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->url(function (TenantInvitation $record) {
                            $whatsappService = app(\App\Services\Communication\WhatsAppService::class);
                            $whatsappService->trackSharing($record, 'whatsapp');
                            return $whatsappService->generateInvitationShareLink($record);
                        })
                        ->openUrlInNewTab()
                        ->visible(fn (TenantInvitation $record) => $record->isPending()),

                    Tables\Actions\Action::make('copy_message')
                        ->label('Copy SMS Message')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->color('info')
                        ->action(function (TenantInvitation $record) {
                            $smsService = app(\App\Services\Communication\SmsService::class);
                            $smsService->trackSending($record);

                            $message = "🏠 HomeBaze Invitation\n\n";
                            $message .= "Hi! {$record->landlord->name} invited you as tenant";
                            if ($record->property) {
                                $message .= " for {$record->property->title}";
                            }
                            $message .= ".\n\nComplete registration: {$record->getInvitationUrl()}\n\n";
                            $message .= "Valid until {$record->expires_at->format('M j, Y')}";

                            Notification::make()
                                ->title('SMS Message Ready')
                                ->body('Send this message to: ' . $record->phone)
                                ->success()
                                ->persistent()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('copy_sms')
                                        ->label('Copy SMS Message')
                                        ->url('javascript:copySmsMessage("' . addslashes($message) . '")')
                                        ->openUrlInNewTab(false)
                                ])
                                ->send();
                        })
                        ->visible(fn (TenantInvitation $record) => $record->isPending()),
                ])
                    ->label('Share Invitation')
                    ->icon('heroicon-o-share')
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
        return parent::getEloquentQuery()
            ->where('landlord_id', Auth::id());
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
