<?php

namespace App\Livewire\Listing;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PlotSize;
use App\Models\PropertySubtype;

class CreateListing extends Component
{
    use WithFileUploads;

    // Wizard Step
    #[Session]
    public $step = 1;

    // Selection State
    #[Session]
    public ?string $selectedCategory = null;

    // Search
    public $areaSearch = '';

    // Form Properties
    #[Validate('nullable|image|max:10240')] 
    public $featured_image;

    #[Session]
    #[Validate('required|string|max:255')]
    public $propertyTitle = '';

    public $propertySlug = '';

    #[Session]
    #[Validate('required|exists:property_types,id')]
    public $property_type_id;

    #[Session]
    #[Validate('nullable|exists:property_subtypes,id')]
    public $property_subtype_id;

    #[Session]
    #[Validate('required|numeric|min:0')]
    public $price;

    #[Session]
    #[Validate('required|in:sale,rent,lease,shortlet')]
    public $listing_type = 'rent';

    public $status = 'available';

    #[Session]
    #[Validate('nullable|required_if:selectedCategory,residential|integer|min:0')]
    public $bedrooms;

    #[Session]
    public $useCustomPlotSize = false;

    #[Session]
    #[Validate('nullable|exclude_unless:selectedCategory,land|required_if:useCustomPlotSize,false|exists:plot_sizes,id')]
    public $plot_size_id;

    #[Session]
    #[Validate('nullable|required_if:useCustomPlotSize,true|numeric|min:0')]
    public $custom_plot_size;

    #[Session]
    #[Validate('nullable|required_if:useCustomPlotSize,true|string|max:50')]
    public $custom_plot_unit = 'sqm';

    #[Session]
    #[Validate('nullable|string')]
    public $description = '';

    #[Session]
    #[Validate('required|exists:states,id')]
    public $state_id;

    #[Session]
    #[Validate('required|exists:cities,id')]
    public $city_id;

    #[Session]
    #[Validate('required|exists:areas,id')]
    public $area_id;

    #[Session]
    #[Validate('required|string|max:500')]
    public $address = '';

    public $createdPropertyId;
    public $createdPropertySlug;
    
    #[Session]
    public $gallery_captions = [];

    #[Validate([
        'gallery_images.*' => 'image|max:10240',
    ])]
    public $gallery_images = [];

    public function updatedGalleryImages()
    {
        $this->validate([
            'gallery_images.*' => 'image|max:10240',
        ]);
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->propertyOwnerProfile) {
            $this->state_id = $user->propertyOwnerProfile->state_id;
            $this->city_id = $user->propertyOwnerProfile->city_id;
        }
    }

    public function updatedStateId()
    {
        $this->city_id = null;
        $this->area_id = null;
        $this->areaSearch = '';
    }

    public function updatedCityId()
    {
        $this->area_id = null;
        $this->areaSearch = '';
    }

    public function removeGalleryImage($index)
    {
        array_splice($this->gallery_images, $index, 1);
        if (isset($this->gallery_captions[$index])) {
            array_splice($this->gallery_captions, $index, 1);
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

    public function getStatesProperty()
    {
        return State::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if (!$this->state_id) return collect();
        return City::where('state_id', $this->state_id)->orderBy('name')->get();
    }

    public function getPropertyTypesProperty()
    {
        return PropertyType::orderBy('name')->get();
    }

    public function getPropertySubtypesProperty()
    {
        if (!$this->property_type_id) return collect();
        
        return PropertySubtype::where('property_type_id', $this->property_type_id)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name')
            ->get();
    }

    public function getPlotSizesProperty()
    {
        return PlotSize::orderBy('sort_order', 'asc')->get();
    }

    public function updatedPropertyTitle($value)
    {
         $this->propertySlug = Str::slug($value);
    }

    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
        
        $targetSlug = match($category) {
            'residential' => 'house',
            'commercial' => 'commercial',
            'land' => 'land',
            'shortlet' => 'shortlet',
            default => $category
        };
        
        $type = PropertyType::where('slug', $targetSlug)->first();
        
        if ($type) {
             $this->property_type_id = $type->id;
        } else {
            $this->property_type_id = null;
        }
    }
    
    public function updatedAreaSearch()
    {
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
        if ($this->step == 1) {
            $this->validate([
                'propertyTitle' => 'required|string|max:255',
                'property_type_id' => 'required',
                'price' => 'required|numeric|min:0',
                'listing_type' => 'required',
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
        Log::info('CreateListing: Starting creation process for user ' . Auth::id());

        if (!$this->area_id && $this->areaSearch) {
             $area = Area::where('name', $this->areaSearch)->where('city_id', $this->city_id)->first();
             if ($area) {
                 $this->area_id = $area->id;
                 Log::info('CreateListing: Auto-resolved area', ['search' => $this->areaSearch, 'id' => $area->id]);
             }
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('CreateListing: Validation failed', ['errors' => $e->validator->errors()->toArray()]);
            session()->flash('error', 'Please check all fields. Some required information might be missing in previous steps.');
            throw $e;
        }

        $user = Auth::user();

        $data = [
            'title' => $this->propertyTitle,
            'slug' => $this->propertySlug ?: Str::slug($this->propertyTitle),
            'property_type_id' => $this->property_type_id,
            'price' => $this->price,
            'listing_type' => $this->listing_type,
            'status' => 'available',
            'bedrooms' => $this->bedrooms,
            'bathrooms' => null,
            'toilets' => null,
            'description' => $this->description,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'address' => $this->address,
            'owner_id' => Auth::id(),
            'property_subtype_id' => $this->property_subtype_id,
            'furnishing_status' => null,
            'plot_size_id' => $this->useCustomPlotSize ? null : $this->plot_size_id,
            'custom_plot_size' => $this->useCustomPlotSize ? $this->custom_plot_size : null,
            'custom_plot_unit' => $this->useCustomPlotSize ? $this->custom_plot_unit : null,
        ];

        // Lazily get or create owner profile
        $propertyOwner = $user->getOrCreatePropertyOwnerProfile();

        try {
            $property = DB::transaction(function () use ($data, $propertyOwner) {
                try {
                    $property = Property::create(array_merge($data, [
                        'entry_slug' => $this->propertySlug,
                        'slug' => $this->propertySlug ?: null,
                        'owner_id' => $propertyOwner->id,
                        'agency_id' => null,
                        'agent_id' => null,
                        'is_published' => true,
                        'moderation_status' => 'pending',
                        'listing_fee_status' => 'waived'
                    ]));
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] === 1062) {
                         throw new \Exception('DUPLICATE_ENTRY');
                    }
                    throw $e;
                }

                // Handle Featured Image Upload
                if ($this->featured_image) {
                    try {
                        $property->addMedia($this->featured_image->getRealPath())
                            ->usingName($this->featured_image->getClientOriginalName())
                            ->toMediaCollection('featured');
                    } catch (\Exception $e) {
                        Log::error('CreateListing: Featured image upload failed', ['error' => $e->getMessage()]);
                        throw $e; 
                    }
                }

                // Handle Gallery Images Upload
                if (!empty($this->gallery_images)) {
                    foreach ($this->gallery_images as $index => $image) {
                        try {
                            $caption = $this->gallery_captions[$index] ?? null;
                            
                            $property->addMedia($image->getRealPath())
                                ->usingName($image->getClientOriginalName())
                                ->withCustomProperties(['caption' => $caption])
                                ->toMediaCollection('gallery');
                        } catch (\Exception $e) {
                            Log::error('CreateListing: Gallery image upload failed', ['index' => $index, 'error' => $e->getMessage()]);
                        }
                    }
                }
                
                return $property;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'DUPLICATE_ENTRY') {
                session()->flash('error', 'A property with this title or slug already exists.');
                return;
            }
            
            Log::error('CreateListing: Transaction failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Something went wrong. Please try again.');
            return;
        }

        $this->reset(['featured_image', 'gallery_images']);

        $this->createdPropertyId = $property->id;
        $this->createdPropertySlug = $property->slug;
        $this->step = 5;
    }

    public function createAnother()
    {
        $this->resetFormState();
        $this->mount();
    }

    public function viewMyListings()
    {
        return redirect()->route('listing.my-listings');
    }

    private function resetFormState()
    {
        $this->reset([
            'step', 
            'selectedCategory', 
            'property_type_id',
            'property_subtype_id',
            'propertyTitle', 
            'propertySlug', 
            'price', 
            'listing_type',
            'status',
            'bedrooms', 
            'description', 
            'state_id',
            'city_id',
            'area_id',
            'areaSearch',
            'address', 
            'featured_image',
            'gallery_images',
            'gallery_captions',
            'createdPropertyId', 
            'createdPropertySlug',
            'plot_size_id',
            'custom_plot_size',
            'custom_plot_unit',
            'useCustomPlotSize'
        ]);
    }

    public function render()
    {
        return view('livewire.listing.create-listing')->layout('layouts.guest-app');
    }
}
