<?php

namespace App\Filament\Landlord\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Property;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\TenantInvitation;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Landlord\Resources\TenantInvitationResource\Pages\EditTenantInvitation;
use App\Filament\Landlord\Resources\TenantInvitationResource\Pages\ListTenantInvitations;
use App\Filament\Landlord\Resources\TenantInvitationResource\Pages\CreateTenantInvitation;
use Dom\Text;

class TenantInvitationResource extends Resource
{
    protected static ?string $model = TenantInvitation::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Tenant Invitations';
    protected static ?string $modelLabel = 'Tenant Invitation';
    protected static ?string $pluralModelLabel = 'Tenant Invitations';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invitation Details')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('phone')
                            ->label('Tenant Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('+234 801 234 5678')
                            ->helperText('Enter the phone number of the person you want to invite as a tenant')
                            ->rules(['regex:/^(\+234|234|0)[789][01]\d{8}$/']),

                        Select::make('property_id')
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

                        Textarea::make('message')
                            ->label('Personal Message')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Add a personal message to include in the invitation (optional)'),

                        DateTimePicker::make('expires_at')
                            ->label('Expiry Date')
                            ->required()
                            ->default(now()->addDays(7))
                            ->minDate(now())
                            ->native(false)
                            ->helperText('When should this invitation expire?'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')
                    ->label('Tenant Phone')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invitation_link')
                    ->label('Invitation Link')
                    ->state(fn(TenantInvitation $record) => $record->getInvitationUrl())
                    ->copyable()
                    ->copyMessage('Invitation link copied!')
                    ->width(2)
                    ->wrap()
                    
                    ->icon('heroicon-m-link')
                    ->iconColor('primary'),

                TextColumn::make('property.title')
                    ->label('Property')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('Any Property'),

                TextColumn::make('accepted_at')
                    ->label('Accepted')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Not accepted')
                    ->sortable(),

                TextColumn::make('tenantUser.name')
                    ->label('Tenant Name')
                    ->placeholder('Not registered')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'expired',
                        'secondary' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->label('Sent')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
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
                    ->options(fn() => Property::where('owner_id', Auth::user()->propertyOwnerProfile?->id ?? Auth::id())->pluck('title', 'id'))
                    ->placeholder('All Properties'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('copy_link')
                        ->label('Copy Link')
                        ->icon('heroicon-o-link')
                        ->action(function (TenantInvitation $record) {
                            $record->update([
                                'link_copied_at' => now(),
                                'link_copy_count' => ($record->link_copy_count ?? 0) + 1
                            ]);

                            Notification::make()
                                ->title('Invitation Link Copied')
                                ->body('Link: ' . $record->getInvitationUrl())
                                ->success()
                                ->send();
                        })
                        ->visible(fn(TenantInvitation $record) => $record->isPending()),

                    Action::make('whatsapp')
                        ->label('Share via WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->action(function (TenantInvitation $record) {
                            $record->update(['last_shared_at' => now()]);

                            $message = "🏠 *HomeBaze Tenant Invitation*\n\n";
                            $message .= "Hi! {$record->landlord->name} has invited you as a tenant";
                            if ($record->property) {
                                $message .= " for *{$record->property->title}*";
                            }
                            $message .= ".\n\n📋 Complete registration: {$record->getInvitationUrl()}\n\n";
                            $message .= "⏰ Valid until {$record->expires_at->format('M j, Y')}\n\nWelcome to HomeBaze! 🎉";

                            $phoneNumber = preg_replace('/[^0-9+]/', '', $record->phone);
                            if (str_starts_with($phoneNumber, '0')) {
                                $phoneNumber = '+234' . substr($phoneNumber, 1);
                            }

                            $whatsappUrl = 'https://wa.me/' . urlencode($phoneNumber) . '?text=' . urlencode($message);

                            return redirect($whatsappUrl);
                        })
                        ->visible(fn(TenantInvitation $record) => $record->isPending()),

                    Action::make('copy_message')
                        ->label('Copy SMS Message')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->color('info')
                        ->action(function (TenantInvitation $record) {
                            $message = "🏠 HomeBaze Invitation\n\n";
                            $message .= "Hi! {$record->landlord->name} invited you as tenant";
                            if ($record->property) {
                                $message .= " for {$record->property->title}";
                            }
                            $message .= ".\n\nComplete registration: {$record->getInvitationUrl()}\n\n";
                            $message .= "Valid until {$record->expires_at->format('M j, Y')}";

                            Notification::make()
                                ->title('SMS Message Ready')
                                ->body("Send this to {$record->phone}: \n\n{$message}")
                                ->success()
                                ->persistent()
                                ->send();
                        })
                        ->visible(fn(TenantInvitation $record) => $record->isPending()),
                ])
                    ->label('Share Invitation')
                    ->icon('heroicon-o-share')
                    ->visible(fn(TenantInvitation $record) => $record->isPending()),

                // Action::make('resend')
                //     ->label('Resend')
                //     ->icon('heroicon-o-arrow-path')
                //     ->action(function (TenantInvitation $record) {
                //         $record->update(['expires_at' => now()->addDays(7)]);

                //         Notification::make()
                //             ->title('Invitation Resent')
                //             ->body('The invitation expiry has been extended for ' . $record->phone)
                //             ->success()
                //             ->send();
                //     })
                //     ->visible(fn(TenantInvitation $record) => $record->isPending()),
                ActionGroup::make([
                    Action::make('cancel')
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
                        ->visible(fn(TenantInvitation $record) => $record->isPending()),

                    EditAction::make()
                        ->visible(fn(TenantInvitation $record) => $record->isPending()),
                ])->label('Manage Invitation'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('quick_invite')
                    ->label('Quick Invite')
                    ->icon('heroicon-o-plus')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->placeholder('+234 801 234 5678')
                            ->rules(['regex:/^(\+234|234|0)[789][01]\d{8}$/']),

                        Select::make('property_id')
                            ->label('Property (Optional)')
                            ->options(function () {
                                $user = Auth::user();
                                $propertyOwner = $user->propertyOwnerProfile;

                                if (!$propertyOwner) {
                                    return Property::where('owner_id', Auth::id())
                                        ->pluck('title', 'id');
                                }

                                return Property::where('owner_id', $propertyOwner->id)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->helperText('Select a specific property for this invitation')
                    ])
                    ->action(function (array $data) {
                        TenantInvitation::create([
                            'phone' => $data['phone'],
                            'property_id' => $data['property_id'] ?? null,
                            'landlord_id' => Auth::id(),
                            'expires_at' => now()->addDays(7),
                            'status' => 'pending',
                        ]);

                        $message = 'Tenant invitation sent to ' . $data['phone'];
                        if (isset($data['property_id'])) {
                            $property = Property::find($data['property_id']);
                            if ($property) {
                                $message .= ' for ' . $property->title;
                            }
                        }

                        Notification::make()
                            ->title('Invitation Created!')
                            ->body($message)
                            ->success()
                            ->send();
                    })
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
            'index' => ListTenantInvitations::route('/'),
            'create' => CreateTenantInvitation::route('/create'),
            'edit' => EditTenantInvitation::route('/{record}/edit'),
        ];
    }
}
