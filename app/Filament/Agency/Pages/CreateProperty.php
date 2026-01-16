<?php

namespace App\Filament\Agency\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyOwner;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PlotSize;
use App\Models\PropertySubtype;
use App\Models\Agent;
use Filament\Actions\Action;
use Illuminate\Validation\ValidationException;

class CreateProperty extends Page
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.agency.pages.create-property';
    protected static ?string $title = 'Create Property';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'create-property';

    #[Session] public $step = 1;
    #[Session] public $selectedCategory = null;
    public $areaSearch = '';

    #[Validate('nullable|image|max:10240')] public $featured_image;

    #[Session] #[Validate('required|string|max:255')] public $propertyTitle = '';
    public $propertySlug = '';

    #[Session] #[Validate('required|exists:property_types,id')] public $property_type_id;
    #[Session] #[Validate('nullable|exists:property_subtypes,id')] public $property_subtype_id;
    #[Session] #[Validate('required|numeric|min:0')] public $price;
    #[Session] #[Validate('required|in:sale,rent,lease,shortlet')] public $listing_type = 'rent';
    #[Session] #[Validate('required|in:available,rented,sold')] public $status = 'available';

    // Agent assignment (agency-specific)
    #[Session] public $agent_id;

    #[Session] public $owner_id;
    #[Session] public $createNewOwner = false;
    #[Session] public $owner_type = 'individual';
    #[Session] public $owner_first_name;
    #[Session] public $owner_last_name;
    #[Session] public $owner_company_name;
    #[Session] public $owner_email;
    #[Session] public $owner_phone;

    #[Session] #[Validate('nullable|required_if:selectedCategory,residential|integer|min:0')] public $bedrooms;
    #[Session] public $useCustomPlotSize = false;
    #[Session] #[Validate('nullable|exclude_unless:selectedCategory,land|required_if:useCustomPlotSize,false|exists:plot_sizes,id')] public $plot_size_id;
    #[Session] #[Validate('nullable|required_if:useCustomPlotSize,true|numeric|min:0')] public $custom_plot_size;
    #[Session] #[Validate('nullable|required_if:useCustomPlotSize,true|string|max:50')] public $custom_plot_unit = 'sqm';
    #[Session] #[Validate('nullable|string')] public $description = '';

    #[Session] #[Validate('required|exists:states,id')] public $state_id;
    #[Session] #[Validate('required|exists:cities,id')] public $city_id;
    #[Session] #[Validate('required|exists:areas,id')] public $area_id;
    #[Session] #[Validate('required|string|max:500')] public $address = '';

    public $createdPropertyId;
    public $createdPropertySlug;
    #[Session] public $gallery_captions = [];
    #[Validate(['gallery_images.*' => 'image|max:10240'])] public $gallery_images = [];

    public function updatedGalleryImages() { $this->validate(['gallery_images.*' => 'image|max:10240']); }

    public function toggleNewOwner() {
        $this->createNewOwner = ! $this->createNewOwner;
        if ($this->createNewOwner) $this->owner_id = null;
    }

    // Get agents belonging to this agency
    public function getAgentsProperty() {
        $agency = Filament::getTenant();
        if (!$agency) return collect();
        return Agent::where('agency_id', $agency->id)
            ->with('user')
            ->get()
            ->mapWithKeys(fn ($agent) => [$agent->id => $agent->user?->name ?? 'Agent #'.$agent->id]);
    }

    public function getOwnersProperty() {
        $agency = Filament::getTenant();
        if (!$agency) return collect();
        
        // Get property owners created by agents in this agency
        return PropertyOwner::whereHas('agent', fn ($q) => $q->where('agency_id', $agency->id))
            ->orderBy('first_name')
            ->orderBy('company_name')
            ->get();
    }

    public function updatedStateId() { $this->city_id = null; $this->area_id = null; $this->areaSearch = ''; }
    public function updatedCityId() { $this->area_id = null; $this->areaSearch = ''; }

    public function removeGalleryImage($index) {
        array_splice($this->gallery_images, $index, 1);
        if (isset($this->gallery_captions[$index])) array_splice($this->gallery_captions, $index, 1);
    }

    public function getFilteredAreasProperty() {
        if (!$this->city_id) return collect();
        return Area::where('city_id', $this->city_id)
            ->when($this->areaSearch, fn($q) => $q->where('name', 'like', '%' . $this->areaSearch . '%'))
            ->limit(50)->get();
    }

    public function getStatesProperty() { return State::orderBy('name')->get(); }
    public function getCitiesProperty() { if (!$this->state_id) return collect(); return City::where('state_id', $this->state_id)->orderBy('name')->get(); }
    public function getPropertyTypesProperty() { return PropertyType::orderBy('name')->get(); }
    public function getPropertySubtypesProperty() {
        if (!$this->property_type_id) return collect();
        return PropertySubtype::where('property_type_id', $this->property_type_id)
            ->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('name')->get();
    }
    public function getPlotSizesProperty() { return PlotSize::orderBy('sort_order', 'asc')->get(); }

    public function updatedPropertyTitle($value) { $this->propertySlug = Str::slug($value); }

    public function selectCategory(string $category): void {
        $this->selectedCategory = $category;
        $targetSlug = match($category) {
            'residential' => 'house', 'commercial' => 'commercial', 'land' => 'land', 'shortlet' => 'shortlet', default => $category
        };
        $type = PropertyType::where('slug', $targetSlug)->first();
        $this->property_type_id = $type ? $type->id : null;
    }
    
    public function updatedAreaSearch() {
        if ($this->area_id) {
            $area = Area::find($this->area_id);
            if ($area && $area->name !== $this->areaSearch) $this->area_id = null;
        }
    }
    
    public function selectArea($id) {
        $this->area_id = $id;
        $area = Area::find($id);
        if ($area) $this->areaSearch = $area->name;
    }

    public function nextStep() {
        if ($this->step == 1) {
            $rules = [
                'propertyTitle' => 'required|string|max:255',
                'property_type_id' => 'required',
                'price' => 'required|numeric|min:0',
                'listing_type' => 'required',
                'status' => 'required',
                'agent_id' => 'required|exists:agents,id', // Agency must assign an agent
            ];
            if ($this->createNewOwner) {
                $rules['owner_type'] = 'required|in:individual,company';
                $rules['owner_email'] = 'nullable|email';
                $rules['owner_phone'] = 'nullable|string';
                if ($this->owner_type === 'individual') {
                    $rules['owner_first_name'] = 'required|string|max:255';
                    $rules['owner_last_name'] = 'required|string|max:255';
                } else {
                    $rules['owner_company_name'] = 'required|string|max:255';
                }
            } else {
                $rules['owner_id'] = 'required|exists:property_owners,id';
            }
            $this->validate($rules);
        }
        if ($this->step < 4) $this->step++;
    }

    public function previousStep() { if ($this->step > 1) $this->step--; }

    public function create() {
        $agency = Filament::getTenant();
        Log::info('Agency CreateProperty: Starting creation for agency ' . $agency?->id);
        
        if (!$this->area_id && $this->areaSearch) {
             $area = Area::where('name', $this->areaSearch)->where('city_id', $this->city_id)->first();
             if ($area) $this->area_id = $area->id;
        }
        try { $this->validate(); } catch (\Illuminate\Validation\ValidationException $e) {
            Notification::make()->danger()->title('Validation Failed')->body('Check fields.')->send(); throw $e;
        }

        if (!$agency) { Notification::make()->danger()->title('Error')->body('No agency context found.')->send(); return; }

        $data = [
            'title' => $this->propertyTitle,
            'slug' => $this->propertySlug ?: Str::slug($this->propertyTitle),
            'property_type_id' => $this->property_type_id,
            'price' => $this->price,
            'listing_type' => $this->listing_type,
            'status' => $this->status,
            'bedrooms' => $this->bedrooms,
            'description' => $this->description,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'address' => $this->address,
            'property_subtype_id' => $this->property_subtype_id,
            'plot_size_id' => $this->useCustomPlotSize ? null : $this->plot_size_id,
            'custom_plot_size' => $this->useCustomPlotSize ? $this->custom_plot_size : null,
            'custom_plot_unit' => $this->useCustomPlotSize ? $this->custom_plot_unit : null,
        ];

        try {
            $property = DB::transaction(function () use ($data, $agency) {
                $ownerId = $this->owner_id;
                if ($this->createNewOwner) {
                    $ownerData = [
                        'type' => $this->owner_type, 'email' => $this->owner_email, 'phone' => $this->owner_phone,
                        'agent_id' => $this->agent_id, 'is_active' => true,
                    ];
                    if ($this->owner_type === 'individual') {
                        $ownerData['first_name'] = $this->owner_first_name; $ownerData['last_name'] = $this->owner_last_name;
                    } else { $ownerData['company_name'] = $this->owner_company_name; }
                    $newOwner = PropertyOwner::create($ownerData);
                    $ownerId = $newOwner->id;
                }

                $property = Property::create(array_merge($data, [
                    'entry_slug' => $this->propertySlug, 'slug' => $this->propertySlug ?: null, 
                    'agent_id' => $this->agent_id,
                    'agency_id' => $agency->id,
                    'owner_id' => $ownerId, 
                    'is_published' => true,
                ]));

                if ($this->featured_image) {
                    $property->addMedia($this->featured_image->getRealPath())
                        ->usingName($this->featured_image->getClientOriginalName())
                        ->toMediaCollection('featured');
                }

                if (!empty($this->gallery_images)) {
                    foreach ($this->gallery_images as $index => $image) {
                        $caption = $this->gallery_captions[$index] ?? null;
                        $property->addMedia($image->getRealPath())->usingName($image->getClientOriginalName())
                            ->withCustomProperties(['caption' => $caption])->toMediaCollection('gallery');
                    }
                }
                return $property;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'DUPLICATE_ENTRY') {
                Notification::make()->danger()->title('Duplicate Property')->send(); return;
            }
            Log::error('Agency CreateProperty: Transaction failed', ['error' => $e->getMessage()]);
            Notification::make()->danger()->title('Creation Failed')->body('Something went wrong.')->send(); return; 
        }

        Notification::make()->success()->title('Property Created Successfully')->send();
        $this->reset(['featured_image', 'gallery_images']);
        $this->createdPropertyId = $property->id;
        $this->createdPropertySlug = $property->slug;
        $this->step = 5;
    }

    public function createAnother() { $this->resetFormState(); $this->mount(); }
    public function viewCreatedProperty() {
        $slug = $this->createdPropertySlug; $this->resetFormState();
        return redirect()->route('property.show', ['property' => $slug]);
    }
    public function backToProperties() { 
        $this->resetFormState(); 
        return redirect()->route('filament.agency.pages.my-properties', ['tenant' => Filament::getTenant()?->slug]); 
    }

    private function resetFormState() {
        $this->reset(['step', 'selectedCategory', 'property_type_id', 'property_subtype_id', 'propertyTitle', 'propertySlug', 'price', 'listing_type', 'status', 'agent_id', 'owner_id', 'createNewOwner', 'owner_type', 'owner_first_name', 'owner_last_name', 'owner_company_name', 'owner_email', 'owner_phone', 'bedrooms', 'description', 'state_id', 'city_id', 'area_id', 'areaSearch', 'address', 'featured_image', 'gallery_images', 'gallery_captions', 'createdPropertyId', 'createdPropertySlug', 'plot_size_id', 'custom_plot_size', 'custom_plot_unit', 'useCustomPlotSize']);
    }
}
