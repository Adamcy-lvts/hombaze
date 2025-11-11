<?php

namespace App\Livewire;

use App\Models\Property;
use App\Services\SimpleRecommendationEngine;
use App\Services\PropertyCommunicationService;
use App\Services\Communication\WhatsAppService;
use App\Services\PropertyViewService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyDetails extends Component
{
    public Property $property;
    public $relatedProperties;
    public $recommendedProperties;
    public $showContactForm = false;
    public $showImageGallery = false;
    public $currentImageIndex = 0;
    public $isFavorited = false;
    public $viewCount = 0;

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
            'agent' => function($query) {
                $query->withCount(['properties' => function($q) {
                    $q->where('is_published', true);
                }]);
            },
            'owner',
            'media'
        ]);

        // Get related properties
        $this->loadRelatedProperties();

        // Get personalized recommendations
        $this->loadRecommendedProperties();

        // Track property view (simplified)
        $this->trackPropertyView();

        // Set the current view count
        $this->viewCount = $this->property->view_count ?? 0;

        // Check if favorited (if user is logged in)
        if (auth()->check()) {
            $this->isFavorited = auth()->user()->savedProperties()->where('property_id', $this->property->id)->exists();
        }
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Please login to save favorites.');
            return;
        }

        if ($this->isFavorited) {
            auth()->user()->savedProperties()->where('property_id', $this->property->id)->delete();
            $this->isFavorited = false;
            session()->flash('message', 'Property removed from favorites.');
        } else {
            auth()->user()->savedProperties()->create([
                'property_id' => $this->property->id,
                'notes' => null,
            ]);
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

            // Track property inquiry (simplified)
            if (auth()->check()) {
                SimpleRecommendationEngine::trackPropertyInquiry(auth()->id(), $this->property->id);
            }

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

        try {
            $whatsappService = new WhatsAppService();
            $contactPhone = PropertyCommunicationService::getContactPhone($this->property);

            if (!$contactPhone) {
                session()->flash('error', 'No contact number available for this property.');
                return;
            }

            if (!$whatsappService->isAvailable()) {
                // Fallback to WhatsApp web link
                $whatsappUrl = $this->getScheduleViewingWhatsAppUrl();
                if ($whatsappUrl) {
                    $this->redirect($whatsappUrl);
                    return;
                }
                session()->flash('error', 'WhatsApp service is not available at the moment.');
                return;
            }

            $user = auth()->user();
            $viewingDetails = [
                'property_title' => $this->property->title,
                'property_url' => route('property.show', $this->property->slug),
                'user_name' => $user->name,
                'user_phone' => $user->phone ?? 'Not provided',
                'user_email' => $user->email,
                'contact_name' => PropertyCommunicationService::getContactName($this->property)
            ];

            $message = $this->formatScheduleViewingMessage($viewingDetails);
            $result = $whatsappService->sendTextMessage($contactPhone, $message);

            if ($result['success']) {
                session()->flash('message', 'Viewing request sent via WhatsApp successfully! The property contact will get back to you soon.');
            } else {
                // Fallback to WhatsApp web link
                $whatsappUrl = $this->getScheduleViewingWhatsAppUrl();
                if ($whatsappUrl) {
                    $this->redirect($whatsappUrl);
                    return;
                }
                session()->flash('error', 'Failed to send WhatsApp message. Please try again or contact the property directly.');
            }
        } catch (\Exception $e) {
            // Fallback to WhatsApp web link
            $whatsappUrl = $this->getScheduleViewingWhatsAppUrl();
            if ($whatsappUrl) {
                $this->redirect($whatsappUrl);
                return;
            }
            session()->flash('error', 'Unable to send viewing request. Please contact the property directly.');
        }
    }

    public function sendWhatsAppMessage()
    {
        if (!auth()->check()) {
            session()->flash('message', 'Please login to send a message.');
            return;
        }

        try {
            $whatsappService = new WhatsAppService();
            $contactPhone = PropertyCommunicationService::getContactPhone($this->property);

            if (!$contactPhone) {
                session()->flash('error', 'No contact number available for this property.');
                return;
            }

            if (!$whatsappService->isAvailable()) {
                // Fallback to WhatsApp web link
                $whatsappUrl = PropertyCommunicationService::getWhatsAppUrl($this->property);
                if ($whatsappUrl) {
                    $this->redirect($whatsappUrl);
                    return;
                }
                session()->flash('error', 'WhatsApp service is not available at the moment.');
                return;
            }

            $user = auth()->user();
            $propertyDetails = [
                'title' => $this->property->title,
                'location' => ($this->property->area?->name ?? 'Unknown Area') . ', ' . ($this->property->city?->name ?? 'Unknown City'),
                'price' => '₦' . number_format($this->property->price),
                'type' => $this->property->propertySubtype?->name ?? 'Unknown Type'
            ];

            $result = $whatsappService->sendPropertyInquiry(
                $contactPhone,
                $this->property->title,
                route('property.show', $this->property->slug),
                $propertyDetails,
                $this->property->getFeaturedImageUrl('medium')
            );

            if ($result['success']) {
                session()->flash('message', 'Message sent via WhatsApp successfully! The property contact will get back to you soon.');
            } else {
                // Fallback to WhatsApp web link
                $whatsappUrl = PropertyCommunicationService::getWhatsAppUrl($this->property);
                if ($whatsappUrl) {
                    $this->redirect($whatsappUrl);
                    return;
                }
                session()->flash('error', 'Failed to send WhatsApp message. Please try again or contact the property directly.');
            }
        } catch (\Exception $e) {
            // Fallback to WhatsApp web link
            $whatsappUrl = PropertyCommunicationService::getWhatsAppUrl($this->property);
            if ($whatsappUrl) {
                $this->redirect($whatsappUrl);
                return;
            }
            session()->flash('error', 'Unable to send message. Please contact the property directly.');
        }
    }

    public function getAgentPhoneNumber()
    {
        return PropertyCommunicationService::getContactPhone($this->property);
    }

    public function getContactEmail()
    {
        return PropertyCommunicationService::getContactEmail($this->property);
    }

    public function getWhatsAppUrl()
    {
        return PropertyCommunicationService::getWhatsAppUrl($this->property);
    }

    public function getSMSUrl()
    {
        return PropertyCommunicationService::getSMSUrl($this->property);
    }

    public function getEmailUrl()
    {
        return PropertyCommunicationService::getEmailUrl($this->property);
    }

    public function getContactName()
    {
        return PropertyCommunicationService::getContactName($this->property);
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

    private function loadRecommendedProperties()
    {
        if (auth()->check()) {
            $engine = new SimpleRecommendationEngine();
            $this->recommendedProperties = $engine->getRecommendationsForProperty(
                $this->property,
                auth()->user(),
                4
            );
        } else {
            // For non-authenticated users, show similar properties
            $this->recommendedProperties = collect();
        }
    }

    private function trackPropertyView()
    {
        try {
            $viewService = new PropertyViewService();

            // Track view with authentication and duplicate prevention
            $wasTracked = $viewService->trackView($this->property);

            // Track for recommendation engine (only for authenticated users)
            if ($wasTracked && auth()->check()) {
                SimpleRecommendationEngine::trackPropertyView(auth()->id(), $this->property->id);
            }

        } catch (\Exception $e) {
            // Silent fail for analytics
            logger()->warning('Failed to track property view', [
                'property_id' => $this->property->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format schedule viewing message for WhatsApp
     */
    private function formatScheduleViewingMessage(array $details): string
    {
        $message = "📅 *Property Viewing Request - HomeBaze*\n\n";
        $message .= "Hello {$details['contact_name']},\n\n";
        $message .= "I would like to schedule a viewing for your property:\n\n";
        $message .= "🏠 *Property:* {$details['property_title']}\n";
        $message .= "👤 *Requester:* {$details['user_name']}\n";
        $message .= "📞 *Contact:* {$details['user_phone']}\n";
        $message .= "📧 *Email:* {$details['user_email']}\n\n";
        $message .= "🔗 *Property Details:* {$details['property_url']}\n\n";
        $message .= "Please let me know your available times for this week or next week. I'm flexible with timing and can accommodate your schedule.\n\n";
        $message .= "Thank you for your time!\n\n";
        $message .= "🔐 *Via HomeBaze - Nigeria's Premier Real Estate Platform*";

        return $message;
    }

    /**
     * Get WhatsApp URL for schedule viewing (fallback)
     */
    private function getScheduleViewingWhatsAppUrl(): ?string
    {
        $phone = PropertyCommunicationService::getContactPhone($this->property);
        if (!$phone) {
            return null;
        }

        $user = auth()->user();
        $message = "📅 Property Viewing Request - HomeBaze\n\n";
        $message .= "Hi! I would like to schedule a viewing for your property: {$this->property->title}\n\n";
        $message .= "My details:\n";
        $message .= "Name: {$user->name}\n";
        $message .= "Phone: " . ($user->phone ?? 'Not provided') . "\n";
        $message .= "Email: {$user->email}\n\n";
        $message .= "Property: " . route('property.show', $this->property->slug) . "\n\n";
        $message .= "Please let me know your available times. Thank you!\n\n";
        $message .= "Via HomeBaze";

        $formattedPhone = $this->formatPhoneNumberForWhatsApp($phone);
        return "https://wa.me/{$formattedPhone}?text=" . urlencode($message);
    }

    /**
     * Format phone number for WhatsApp URL
     */
    private function formatPhoneNumberForWhatsApp(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If phone starts with +234, remove + for wa.me URL
        if (str_starts_with($phone, '+234')) {
            return substr($phone, 1);
        }

        // If phone starts with 234, keep as is
        if (str_starts_with($phone, '234')) {
            return $phone;
        }

        // If phone starts with 0, replace with 234
        if (str_starts_with($phone, '0')) {
            return '234' . substr($phone, 1);
        }

        // Otherwise assume it's a Nigerian number without country code
        return '234' . $phone;
    }

    public function render()
    {
        $title = $this->property->title . ' - ' . ($this->property->city?->name ?? 'Unknown City');

        // Prepare Open Graph data for WhatsApp link previews
        $propertyTypeName = $this->property->propertySubtype?->name ?? 'property';
        $areaName = $this->property->area?->name ?? 'Unknown Area';
        $cityName = $this->property->city?->name ?? 'Unknown City';

        $ogData = [
            'title' => $this->property->title,
            'description' => $this->property->description ?
                Str::limit(strip_tags($this->property->description), 150) :
                "Discover this amazing {$propertyTypeName} in {$areaName}, {$cityName}. Price: ₦" . number_format($this->property->price),
            'image' => $this->property->getFeaturedImageUrl('medium'),
            'url' => route('property.show', $this->property->slug),
            'type' => 'article',
            'site_name' => 'HomeBaze',
            'price' => $this->property->price,
            'currency' => 'NGN',
            'location' => ($this->property->area?->name ?? 'Unknown Area') . ', ' . ($this->property->city?->name ?? 'Unknown City'),
            'property_type' => $this->property->propertySubtype?->name ?? 'Unknown Type'
        ];

        return view('livewire.property-details')
            ->layout('layouts.livewire-property', [
                'title' => $title,
                'property' => $this->property,
                'ogData' => $ogData
            ]);
    }
}
