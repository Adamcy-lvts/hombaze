<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads; // For image uploads
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
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

    #[Session]
    #[Validate('required|in:available,rented,sold')]
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

    public function removeGalleryImage($index)
    {
        array_splice($this->gallery_images, $index, 1);
        // Also remove the corresponding caption if it exists, need to re-index array
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
            'shortlet' => 'shortlet', // Assuming shortlet has its own type or uses residential? logic might need adjust if shortlet is a listing type not property type. 
            // Actually originally it was just mapping to slugs
            default => $category
        };
        
        // Find type by slug
        $type = PropertyType::where('slug', $targetSlug)->first();
        
        // Fallback for residential if 'house' not found?? 
        // Based on original logic:
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
        Log::info('CreateProperty: Starting creation process for user ' . Auth::id());

        // UX: detailed auto-resolution of area
        if (!$this->area_id && $this->areaSearch) {
             $area = Area::where('name', $this->areaSearch)->where('city_id', $this->city_id)->first();
             if ($area) {
                 $this->area_id = $area->id;
                 Log::info('CreateProperty: Auto-resolved area', ['search' => $this->areaSearch, 'id' => $area->id]);
             }
        }

        try {
            $this->validate();
            Log::info('CreateProperty: Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('CreateProperty: Validation failed', ['errors' => $e->validator->errors()->toArray()]);
            
            Notification::make()
                ->danger()
                ->title('Validation Failed')
                ->body('Please check all fields. Some required information might be missing in previous steps.')
                ->send();
                
            throw $e;
        }

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
            'property_subtype_id' => $this->property_subtype_id,
            'furnishing_status' => null, // Optional now
            'plot_size_id' => $this->useCustomPlotSize ? null : $this->plot_size_id,
            'custom_plot_size' => $this->useCustomPlotSize ? $this->custom_plot_size : null,
            'custom_plot_unit' => $this->useCustomPlotSize ? $this->custom_plot_unit : null,
        ];

        // Resolve Property Owner
        $propertyOwner = PropertyOwner::where('user_id', Auth::id())->first();
        
        if (!$propertyOwner) {
            Log::error('CreateProperty: No PropertyOwner record found for user ' . Auth::id());
            Notification::make()
                ->danger()
                ->title('Account Error')
                ->body('Your landlord account is not fully set up. Please contact support.')
                ->send();
            return;
        }

        try {
            $property = DB::transaction(function () use ($data, $propertyOwner) {
                try {
                    $property = Property::create(array_merge($data, [
                        'entry_slug' => $this->propertySlug, // Pass raw for logic check below
                        'slug' => $this->propertySlug ?: null, // Pass null to let Model handle uniqueness generation
                        'owner_id' => $propertyOwner->id,
                        'agency_id' => null,
                        'agent_id' => null,
                    ]));
                    Log::info('CreateProperty: Property created', ['id' => $property->id, 'slug' => $property->slug]);
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] === 1062) { // Duplicate entry
                         throw new \Exception('DUPLICATE_ENTRY');
                    }
                    throw $e;
                }

                // Handle Featured Image Upload
                if ($this->featured_image) {
                    Log::info('CreateProperty: Processing featured image upload');
                    try {
                        $property->addMedia($this->featured_image->getRealPath())
                            ->usingName($this->featured_image->getClientOriginalName())
                            ->toMediaCollection('featured');
                        Log::info('CreateProperty: Featured image uploaded successfully');
                    } catch (\Exception $e) {
                        Log::error('CreateProperty: Featured image upload failed', ['error' => $e->getMessage()]);
                        throw $e; 
                    }
                }

                // Handle Gallery Images Upload
                if (!empty($this->gallery_images)) {
                    Log::info('CreateProperty: Processing gallery images upload', ['count' => count($this->gallery_images)]);
                    foreach ($this->gallery_images as $index => $image) {
                        try {
                            $caption = $this->gallery_captions[$index] ?? null;
                            
                            $property->addMedia($image->getRealPath())
                                ->usingName($image->getClientOriginalName())
                                ->withCustomProperties(['caption' => $caption])
                                ->toMediaCollection('gallery');
                        } catch (\Exception $e) {
                            Log::error('CreateProperty: Gallery image upload failed', ['index' => $index, 'error' => $e->getMessage()]);
                            // Continue uploading others or throw? Let's log and continue for gallery.
                        }
                    }
                }
                
                return $property;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'DUPLICATE_ENTRY') {
                Log::warning('CreateProperty: Duplicate entry detected');
                Notification::make()
                   ->danger()
                   ->title('Duplicate Property')
                   ->body('A property with this title or slug already exists. Please verify your property list.')
                   ->send();
                return;
            }
            
            // Re-throw if it's not our handled duplicate error
            Log::error('CreateProperty: Transaction failed', ['error' => $e->getMessage()]);
            Notification::make()
                ->danger()
                ->title('Creation Failed')
                ->body('Something went wrong. Please try again.')
                ->send();
            return; // Or throw $e if you want standard error page
        }

        Notification::make()
            ->success()
            ->title('Property Created Successfully')
            ->send();

        // Prevent "FileNotPreviewableException" by clearing the consumed image before re-render
        $this->reset(['featured_image', 'gallery_images']);

        // Instead of redirecting, show success step
        $this->createdPropertyId = $property->id;
        $this->createdPropertySlug = $property->slug;
        $this->step = 5;
    }

    public function createAnother()
    {
        $this->resetFormState();
        $this->mount();
    }

    public function viewCreatedProperty()
    {
        $slug = $this->createdPropertySlug;
        $this->resetFormState();
        return redirect()->route('property.show', ['property' => $slug]);
    }

    public function backToProperties()
    {
        $this->resetFormState();
        return redirect()->to(PropertyResource::getUrl('index'));
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
            'areaSearch', // Ensure search is cleared
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
}
