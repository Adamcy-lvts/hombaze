<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Filament\Landlord\Resources\TenantResource;

class CreateTenant extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected string $view = 'filament.landlord.pages.create-tenant';

    protected static ?string $title = 'Add Tenant';
    
    protected static bool $shouldRegisterNavigation = false;

    // Wizard Step
    #[Session]
    public $step = 1;

    // Step 1: Personal Information
    #[Session]
    #[Validate('required|string|max:255')]
    public $first_name = '';

    #[Session]
    #[Validate('required|string|max:255')]
    public $last_name = '';

    #[Session]
    #[Validate('nullable|email|max:255')]
    public $email = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $phone = '';

    #[Session]
    #[Validate('nullable|date')]
    public $date_of_birth;

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $nationality = '';

    // Step 2: Employment Information
    #[Session]
    #[Validate('nullable|string')]
    public $employment_status = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $employer_name = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $occupation = '';

    #[Session]
    #[Validate('nullable|numeric|min:0')]
    public $monthly_income;

    // Step 3: Identification & Emergency
    #[Session]
    #[Validate('nullable|string')]
    public $identification_type = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $identification_number = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $emergency_contact_name = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $emergency_contact_phone = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $guarantor_name = '';

    #[Session]
    #[Validate('nullable|string|max:255')]
    public $guarantor_phone = '';

    #[Session]
    #[Validate('nullable|email|max:255')]
    public $guarantor_email = '';

    #[Session]
    #[Validate('nullable|string|max:1000')]
    public $notes = '';

    public $createdTenantId;
    public $createdTenantName;

    public function nextStep()
    {
        if ($this->step == 1) {
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
        }
        
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function create()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        try {
            $tenant = Tenant::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'date_of_birth' => $this->date_of_birth,
                'nationality' => $this->nationality,
                'employment_status' => $this->employment_status,
                'employer_name' => $this->employer_name,
                'occupation' => $this->occupation,
                'monthly_income' => $this->monthly_income,
                'identification_type' => $this->identification_type,
                'identification_number' => $this->identification_number,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'guarantor_name' => $this->guarantor_name,
                'guarantor_phone' => $this->guarantor_phone,
                'guarantor_email' => $this->guarantor_email,
                'notes' => $this->notes,
                'is_active' => true,
                'landlord_id' => Auth::id(),
            ]);

            Notification::make()
                ->success()
                ->title('Tenant Added Successfully')
                ->send();

            $this->createdTenantId = $tenant->id;
            $this->createdTenantName = $tenant->name;
            $this->step = 4; // Success step

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Creating Tenant')
                ->body('Something went wrong. Please try again.')
                ->send();
        }
    }

    public function createAnother()
    {
        $this->resetFormState();
    }

    public function viewTenantsList()
    {
        $this->resetFormState();
        return redirect()->to(TenantResource::getUrl('index'));
    }

    public function backToTenants()
    {
        $this->resetFormState();
        return redirect()->route('filament.landlord.pages.tenants-list');
    }

    private function resetFormState()
    {
        $this->reset([
            'step',
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'nationality',
            'employment_status',
            'employer_name',
            'occupation',
            'monthly_income',
            'identification_type',
            'identification_number',
            'emergency_contact_name',
            'emergency_contact_phone',
            'guarantor_name',
            'guarantor_phone',
            'guarantor_email',
            'notes',
            'createdTenantId',
            'createdTenantName',
        ]);
    }
}
