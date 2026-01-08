<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads; // For image uploads
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Filament\Landlord\Resources\PropertyResource;

class CreateProperty extends Page
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.landlord.pages.create-property';

    protected static ?string $title = 'Create Property';
    
    protected static bool $shouldRegisterNavigation = false;

    // Wizard Step
    #[Session]
    public $step = 1;

    // Selection State
    #[Session]
    public ?string $selectedCategory = null;

    // Search
    public $areaSearch = '';

    // Form Properties
    #[Validate('required|image|max:10240')] // 10MB Max
    public $featured_image;

    #[Session]
    #[Validate('required|string|max:255')]
    public $propertyTitle = '';

    #[Session]
    public $propertySlug = '';

    #[Session]
    #[Validate('required|exists:property_types,id')]
    public $property_type_id;

    #[Session]
    #[Validate('required|numeric|min:0')]
    public $price;

    #[Session]
    #[Validate('required|in:sale,rent,lease,shortlet')]
    public $listing_type = 'rent';

    #[Session]
    #[Validate('required|in:available,rented,sold')]
    public $status = 'available';

    #[Session]
    #[Validate('required|integer|min:0')]
    public $bedrooms;

    #[Session]
    #[Validate('nullable|string')]
    public $description = '';

    #[Session]
    #[Validate('required|exists:states,id')]
    public $state_id;

    #[Session]
    #[Validate('required|exists:cities,id')] // Assuming cities table exists and has id
    public $city_id;

    #[Session]
    #[Validate('required|exists:areas,id')]
    public $area_id;

    #[Session]
    #[Validate('required|string|max:500')]
    public $address = '';

    public function mount(): void
    {
        // Only run if not already set (Session handles it, but initial load might need it)
        if (!$this->state_id || !$this->city_id) {
            // Auto-fill State and City from User Profile
            $user = Auth::user();
            if ($user) {
                $ownerProfile = $user->propertyOwnerProfile;
                // Only fill if not already set
                $this->state_id = $this->state_id ?? ($ownerProfile?->state_id ?? $user->profile?->state_id);
                $this->city_id = $this->city_id ?? ($ownerProfile?->city_id ?? $user->profile?->city_id);
            }
        }
        
        // Initialize area search if area is already selected
        if ($this->area_id) {
            $area = Area::find($this->area_id);
            if ($area) {
                $this->areaSearch = $area->name;
            }
        }
    }

    public function getFilteredAreasProperty()
    {
        if (!$this->city_id) return collect();
        
        return Area::where('city_id', $this->city_id)
            ->when($this->areaSearch, fn($q) => $q->where('name', 'like', '%' . $this->areaSearch . '%'))
            ->limit(50)
            ->get();
    }
    
    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
        
        $type = PropertyType::where('slug', $category)->first();
        if ($type) {
             $this->property_type_id = $type->id;
        }
    } 

    public function updatedPropertyTitle($value)
    {
        $this->propertySlug = Str::slug($value);
    }
    
    public function updatedAreaSearch()
    {
        // When user types, clear the selected ID to ensure they pick a valid one
        // Check if current text matches exactly what's selected, if so, don't clear
        if ($this->area_id) {
            $area = Area::find($this->area_id);
            if ($area && $area->name !== $this->areaSearch) {
                 $this->area_id = null;
            }
        }
    }
    
    public function selectArea($id)
    {
        $this->area_id = $id;
        $area = Area::find($id);
        if ($area) {
            $this->areaSearch = $area->name;
        }
    }

    public function nextStep()
    {
        if ($this->step == 2) {
            $this->validate([
                'propertyTitle' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'listing_type' => 'required',
                'status' => 'required',
            ]);
        }
        
        if ($this->step < 4) {
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
        $this->validate();

        $data = [
            'title' => $this->propertyTitle,
            'slug' => $this->propertySlug ?: Str::slug($this->propertyTitle),
            'property_type_id' => $this->property_type_id,
            'price' => $this->price,
            'listing_type' => $this->listing_type,
            'status' => $this->status,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => null, // Optional now
            'toilets' => null,
            'description' => $this->description,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'address' => $this->address,
            'owner_id' => Auth::id(), // Assign to current user
            'property_subtype_id' => null, // Optional now
            'furnishing_status' => null, // Optional now
        ];

        $property = Property::create($data);

        // Handle Image Upload
        if ($this->featured_image) {
            $property->addMedia($this->featured_image->getRealPath())
                ->usingName($this->featured_image->getClientOriginalName())
                ->toMediaCollection('featured');
        }

        Notification::make()
            ->success()
            ->title('Property Created Successfully')
            ->send();

        $this->redirect(PropertyResource::getUrl('index'));
    }
}
