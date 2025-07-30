<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PropertyDetails extends Component
{
    public Property $property;
    public $relatedProperties;
    public $showContactForm = false;
    public $showImageGallery = false;
    public $currentImageIndex = 0;
    public $isFavorited = false;

    // Contact form fields
    public $inquiryName = '';
    public $inquiryEmail = '';
    public $inquiryPhone = '';
    public $inquiryMessage = '';

    public function mount(Property $property)
    {
        $this->property = $property;
        
        // Load all necessary relationships
        $this->property->load([
            'city.state',
            'area',
            'propertyType',
            'propertySubtype',
            'features' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            },
            'agency',
            'agent',
            'owner',
            'media'
        ]);

        // Get related properties
        $this->loadRelatedProperties();
        
        // Track property view
        $this->trackPropertyView();
        
        // Check if favorited (if user is logged in)
        if (auth()->check()) {
            $this->isFavorited = auth()->user()->favoritedProperties()->where('property_id', $this->property->id)->exists();
        }
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Please login to save favorites.');
            return;
        }

        if ($this->isFavorited) {
            auth()->user()->favoritedProperties()->detach($this->property->id);
            $this->isFavorited = false;
            session()->flash('message', 'Property removed from favorites.');
        } else {
            auth()->user()->favoritedProperties()->attach($this->property->id);
            $this->isFavorited = true;
            session()->flash('message', 'Property added to favorites.');
        }
    }

    public function toggleContactForm()
    {
        $this->showContactForm = !$this->showContactForm;
        if ($this->showContactForm) {
            $this->inquiryMessage = "Hi, I'm interested in {$this->property->title}. Please provide more details.";
        }
    }

    public function submitInquiry()
    {
        $this->validate([
            'inquiryName' => 'required|string|max:255',
            'inquiryEmail' => 'required|email|max:255',
            'inquiryPhone' => 'required|string|max:20',
            'inquiryMessage' => 'required|string|max:1000',
        ]);

        try {
            DB::table('property_inquiries')->insert([
                'property_id' => $this->property->id,
                'user_id' => auth()->id(),
                'name' => $this->inquiryName,
                'email' => $this->inquiryEmail,
                'phone' => $this->inquiryPhone,
                'message' => $this->inquiryMessage,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            session()->flash('message', 'Your inquiry has been sent successfully! We\'ll get back to you soon.');
            $this->reset(['inquiryName', 'inquiryEmail', 'inquiryPhone', 'inquiryMessage']);
            $this->showContactForm = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send inquiry. Please try again.');
        }
    }

    public function openImageGallery($index = 0)
    {
        $this->currentImageIndex = $index;
        $this->showImageGallery = true;
    }

    public function closeImageGallery()
    {
        $this->showImageGallery = false;
    }

    public function nextImage()
    {
        $totalImages = $this->property->getMedia('gallery')->count();
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex + 1) % $totalImages;
        }
    }

    public function previousImage()
    {
        $totalImages = $this->property->getMedia('gallery')->count();
        if ($totalImages > 0) {
            $this->currentImageIndex = ($this->currentImageIndex - 1 + $totalImages) % $totalImages;
        }
    }

    public function scheduleViewing()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Please login to schedule a viewing.');
            return;
        }

        // Here you would typically redirect to a viewing scheduling form
        // or open a modal with date/time selection
        session()->flash('message', 'Viewing scheduling feature coming soon!');
    }

    private function loadRelatedProperties()
    {
        $this->relatedProperties = Property::with(['city', 'state', 'propertyType'])
            ->where('city_id', $this->property->city_id)
            ->where('id', '!=', $this->property->id)
            ->where('status', 'available')
            ->where('is_published', true)
            ->orderBy('is_featured', 'desc')
            ->limit(4)
            ->get();
    }

    private function trackPropertyView()
    {
        try {
            // Increment view count
            $this->property->increment('view_count');

            // Track detailed view analytics
            DB::table('property_views')->insert([
                'property_id' => $this->property->id,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referrer' => request()->header('referer'),
                'viewed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silent fail for analytics
            logger()->warning('Failed to track property view', [
                'property_id' => $this->property->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $title = $this->property->title . ' - ' . $this->property->city->name;
        
        return view('livewire.property-details')
            ->layout('layouts.livewire-property', ['title' => $title]);
    }
}
