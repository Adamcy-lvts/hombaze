<?php

namespace App\Filament\Landlord\Pages;

use App\Models\RentPayment;
use App\Models\Tenant;
use App\Models\Property;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateFreeReceipt extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Create Receipt';
    protected static ?string $title = 'Create Receipt';
    protected static ?string $slug = 'create-free-receipt';
    protected string $view = 'filament.landlord.pages.create-free-receipt';
    protected static string | \UnitEnum | null $navigationGroup = 'Financial Management';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public ?RentPayment $createdReceipt = null;

    public function mount(): void
    {
        $this->form->fill([
            'use_existing_tenant' => true,
            'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'transfer',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Recipient & Property - Combined Section
                Section::make('Recipient & Property')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        // Tenant Selection
                        Toggle::make('use_existing_tenant')
                            ->label('Use existing tenant')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),

                        Select::make('tenant_id')
                            ->label('Select Tenant')
                            ->options(fn () => Tenant::where('landlord_id', Auth::id())
                                ->orderBy('first_name')
                                ->get()
                                ->mapWithKeys(fn ($t) => [$t->id => $t->name]))
                            ->searchable()
                            ->required(fn ($get) => $get('use_existing_tenant'))
                            ->visible(fn ($get) => $get('use_existing_tenant')),

                        Select::make('property_id')
                            ->label('Select Property')
                            ->options(fn () => Property::whereHas('owner', fn ($q) => $q->where('user_id', Auth::id()))
                                ->orderBy('title')
                                ->get()
                                ->mapWithKeys(fn ($p) => [$p->id => $p->title]))
                            ->searchable()
                            ->visible(fn ($get) => $get('use_existing_tenant')),

                        // Manual Entry Fields
                        TextInput::make('manual_tenant_name')
                            ->label('Recipient Name')
                            ->required(fn ($get) => !$get('use_existing_tenant'))
                            ->visible(fn ($get) => !$get('use_existing_tenant')),

                        TextInput::make('manual_tenant_phone')
                            ->label('Phone')
                            ->tel()
                            ->visible(fn ($get) => !$get('use_existing_tenant')),

                        TextInput::make('manual_property_title')
                            ->label('Property / Description')
                            ->placeholder('e.g., 3BR Flat at Lekki')
                            ->visible(fn ($get) => !$get('use_existing_tenant'))
                            ->columnSpanFull(),
                    ]),

                // Payment Information - Streamlined
                Section::make('Payment Information')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('₦')
                            ->required()
                            ->placeholder('0.00'),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'transfer' => 'Transfer',
                                'cash' => 'Cash',
                                'pos' => 'POS',
                                'card' => 'Card',
                            ])
                            ->required()
                            ->default('transfer'),

                        DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now()),

                        TextInput::make('payment_for')
                            ->label('Payment For')
                            ->placeholder('e.g., Rent, Service Charge, Deposit')
                            ->columnSpan(2),

                        TextInput::make('receipt_number')
                            ->label('Receipt #')
                            ->required()
                            ->unique('rent_payments', 'receipt_number')
                            ->default(fn () => 'RCP-' . strtoupper(Str::random(8))),
                    ]),

                // Optional Period Dates - Collapsible
                Section::make('Period Dates (Optional)')
                    ->icon('heroicon-o-calendar')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('custom_start_date')
                            ->label('Start Date'),
                        DatePicker::make('custom_end_date')
                            ->label('End Date'),
                    ]),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        try {
            $amount = (float) ($data['amount'] ?? 0);

            $receiptData = [
                'landlord_id' => Auth::id(),
                'amount' => $amount,
                'late_fee' => 0,
                'discount' => 0,
                'deposit' => 0,
                'net_amount' => $amount,
                'balance_due' => 0,
                'payment_method' => $data['payment_method'],
                'payment_date' => $data['payment_date'],
                'due_date' => $data['payment_date'], // Use payment date as due date
                'payment_for' => $data['payment_for'] ?? null,
                'custom_start_date' => $data['custom_start_date'] ?? null,
                'custom_end_date' => $data['custom_end_date'] ?? null,
                'receipt_number' => $data['receipt_number'],
                'status' => 'paid',
                'processed_by' => Auth::id(),
                'is_manual_entry' => !($data['use_existing_tenant'] ?? true),
            ];

            // Tenant & Property data
            if ($data['use_existing_tenant'] ?? true) {
                $receiptData['tenant_id'] = $data['tenant_id'] ?? null;
                $receiptData['property_id'] = $data['property_id'] ?? null;
            } else {
                $receiptData['manual_tenant_name'] = $data['manual_tenant_name'] ?? null;
                $receiptData['manual_tenant_phone'] = $data['manual_tenant_phone'] ?? null;
                $receiptData['manual_property_title'] = $data['manual_property_title'] ?? null;
            }

            $this->createdReceipt = RentPayment::create($receiptData);

            Notification::make()
                ->success()
                ->title('Receipt Created Successfully')
                ->body('Receipt #' . $data['receipt_number'] . ' has been saved.')
                ->send();

        } catch (\Exception $e) {
            \Log::error('[CreateFreeReceipt] Error creating receipt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title('Error Creating Receipt')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function viewReceipt(): void
    {
        if ($this->createdReceipt) {
            $this->redirect(route('filament.landlord.resources.rent-payments.view-receipt', $this->createdReceipt));
        }
    }

    public function createAnother(): void
    {
        $this->createdReceipt = null;
        $this->form->fill([
            'use_existing_tenant' => true,
            'use_existing_property' => true,
            'receipt_number' => 'RCP-' . strtoupper(Str::random(8)),
            'payment_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'payment_method' => 'transfer',
            'late_fee' => 0,
            'discount' => 0,
            'deposit' => 0,
            'tenant_id' => null,
            'property_id' => null,
            'lease_id' => null,
            'amount' => null,
            'payment_for_period' => null,
            'notes' => null,
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Receipt')
                ->icon('heroicon-o-check')
                ->action('create'),
        ];
    }
}
