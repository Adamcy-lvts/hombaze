<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyOwner;
use App\Models\Area;
use App\Models\PlotSize;
use App\Models\PropertySubtype;
use App\Filament\Landlord\Resources\PropertyResource;

class EditProperty extends Page
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-pencil-square';

    protected string $view = 'filament.landlord.pages.edit-property';

    protected static ?string $title = 'Edit Property';
    
    protected static bool $shouldRegisterNavigation = false;

    // Record ID from Query String
    #[Url]
    public ?string $record = null;

    public ?Property $property = null;

    // Wizard Step
    public $step = 1;

    // Selection State
    public ?string $selectedCategory = null;

    // Search
    public $areaSearch = '';

    // Form Properties
    #[Validate('nullable|image|max:10240')] 
    public $featured_image; // Only for new upload

    public $existing_featured_image; // For display

    #[Validate('required|string|max:255')]
    public $propertyTitle = '';

    public $propertySlug = '';

    #[Validate('required|exists:property_types,id')]
    public $property_type_id;

    #[Validate('nullable|exists:property_subtypes,id')]
    public $property_subtype_id;

    #[Validate('required|numeric|min:0')]
    public $price;

    #[Validate('required|in:sale,rent,lease,shortlet')]
    public $listing_type;

    #[Validate('required|in:available,rented,sold')]
    public $status;

    #[Validate('nullable|required_if:selectedCategory,residential|integer|min:0')]
    public $bedrooms;

    public $useCustomPlotSize = false;

    #[Validate('nullable|exclude_unless:selectedCategory,land|required_if:useCustomPlotSize,false|exists:plot_sizes,id')]
    public $plot_size_id;

    #[Validate('nullable|required_if:useCustomPlotSize,true|numeric|min:0')]
    public $custom_plot_size;

    #[Validate('nullable|required_if:useCustomPlotSize,true|string|max:50')]
    public $custom_plot_unit = 'sqm';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|exists:states,id')]
    public $state_id;

    #[Validate('required|exists:cities,id')]
    public $city_id;

    #[Validate('required|exists:areas,id')]
    public $area_id;

    #[Validate('required|string|max:500')]
    public $address = '';

    #[Validate([
        'new_gallery_images.*' => 'image|max:10240',
    ])]
    public $new_gallery_images = [];
    public $new_gallery_captions = [];
    public $existing_gallery_captions = [];
    public $existingGallery = [];

    public function mount(): void
    {
        if (!$this->record) {
             $this->redirect(route('filament.landlord.pages.my-properties'));
             return;
        }

        $this->property = Property::where('id', $this->record)->firstOrFail();

        // Verify Ownership
        $owner = PropertyOwner::where('user_id', Auth::id())->first();
        if (!$owner || $this->property->owner_id !== $owner->id) {
            abort(403, 'Unauthorized access to this property.');
        }

        // Load Data
        $this->propertyTitle = $this->property->title;
        $this->propertySlug = $this->property->slug;
        $this->property_type_id = $this->property->property_type_id;
        $this->property_subtype_id = $this->property->property_subtype_id;
        $this->price = $this->property->price;
        $this->listing_type = $this->property->listing_type;
        $this->status = $this->property->status;
        $this->bedrooms = $this->property->bedrooms;
        $this->description = $this->property->description;
        $this->state_id = $this->property->state_id;
        $this->city_id = $this->property->city_id;
        $this->area_id = $this->property->area_id;
        $this->address = $this->property->address;
        
        $this->plot_size_id = $this->property->plot_size_id;
        $this->custom_plot_size = $this->property->custom_plot_size;
        $this->custom_plot_unit = $this->property->custom_plot_unit ?? 'sqm';
        $this->useCustomPlotSize = !empty($this->custom_plot_size);

    // Load Existing Image
        $this->existing_featured_image = $this->property->getFirstMediaUrl('featured');
        
        // Load Gallery
        $this->existingGallery = $this->property->getMedia('gallery');
        foreach($this->existingGallery as $media) {
            $this->existing_gallery_captions[$media->id] = $media->getCustomProperty('caption');
        }




        // Determine Category
        $type = $this->property->propertyType;
        if ($type && $type->slug === 'land') {
            $this->selectedCategory = 'land';
        } elseif ($type && $type->slug === 'commercial') {
            $this->selectedCategory = 'commercial';
        } else {
            $this->selectedCategory = 'residential';
        }

        // Init Area Search
        if ($this->area_id) {
            $area = Area::find($this->area_id);
            if ($area) {
                $this->areaSearch = $area->name;
            }
        }
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

    public function getPropertyTypesProperty()
    {
        // ... (existing)
        return PropertyType::orderBy('name')->get();
    }

    public function getFilteredAreasProperty()
    {
        if (!$this->city_id) return collect();
        
        return Area::where('city_id', $this->city_id)
            ->when($this->areaSearch, fn($q) => $q->where('name', 'like', '%' . $this->areaSearch . '%'))
            ->limit(50)
            ->get();
    }

    // ... (rest kept for context reference if needed, but I aim to slot this in cleanly)
    
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

    public function updatedNewGalleryImages()
    {
        $this->validate([
            'new_gallery_images.*' => 'image|max:10240',
        ]);
    }

    public function removeNewGalleryImage($index)
    {
        array_splice($this->new_gallery_images, $index, 1);
        if (isset($this->new_gallery_captions[$index])) {
            array_splice($this->new_gallery_captions, $index, 1);
        }
    }

    public function deleteGalleryImage($mediaId)
    {
        $media = $this->property->media()->find($mediaId);
        if ($media) {
            $media->delete();
            // Refresh gallery
            $this->existingGallery = $this->property->getMedia('gallery');
            unset($this->existing_gallery_captions[$mediaId]);
            
            Notification::make()
                ->success()
                ->title('Image Deleted')
                ->send();
        }
    }

    public function update()
    {
        $this->validate();

        $data = [
            'title' => $this->propertyTitle,
            'slug' => $this->propertySlug,
            'property_type_id' => $this->property_type_id,
            'property_subtype_id' => $this->property_subtype_id,
            'price' => $this->price,
            'listing_type' => $this->listing_type,
            'status' => $this->status,
            'bedrooms' => $this->bedrooms,
            'plot_size_id' => $this->plot_size_id,
            'custom_plot_size' => $this->custom_plot_size,
            'custom_plot_unit' => $this->custom_plot_unit,
            'description' => $this->description,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'address' => $this->address,
        ];

        $this->property->update($data);

        // Update Existing Gallery Captions
        foreach ($this->existing_gallery_captions as $mediaId => $caption) {
             $media = $this->property->media()->find($mediaId);
             if ($media) {
                 $media->setCustomProperty('caption', $caption);
                 $media->save();
             }
        }

        // Handle Featured Image Upload
        if ($this->featured_image) {
            try {
                $this->property->clearMediaCollection('featured');
                $this->property->addMedia($this->featured_image->getRealPath())
                    ->usingName($this->featured_image->getClientOriginalName())
                    ->toMediaCollection('featured');
            } catch (\Exception $e) {
                Log::error('EditProperty: Featured image upload failed', ['error' => $e->getMessage()]);
                Notification::make()
                ->danger()
                ->title('Image Upload Failed')
                ->body('Property updated, but featured image could not be saved.')
                ->send();
            }
        }

        // Handle New Gallery Images
        if (!empty($this->new_gallery_images)) {
            foreach ($this->new_gallery_images as $index => $image) {
                try {
                    $caption = $this->new_gallery_captions[$index] ?? null;
                    
                    $this->property->addMedia($image->getRealPath())
                        ->usingName($image->getClientOriginalName())
                        ->withCustomProperties(['caption' => $caption])
                        ->toMediaCollection('gallery');
                } catch (\Exception $e) {
                    Log::error('EditProperty: Gallery upload failed', ['index' => $index, 'error' => $e->getMessage()]);
                }
            }
        }

        Notification::make()
            ->success()
            ->title('Property Updated Successfully')
            ->send();

        return redirect()->route('filament.landlord.pages.my-properties');
    }

    public function cancel()
    {
         return redirect()->route('filament.landlord.pages.my-properties');
    }
}
