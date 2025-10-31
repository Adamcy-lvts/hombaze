<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\User;
use App\Services\Communication\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppMessageHandler
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle incoming WhatsApp message
     */
    public function handleIncomingMessage(array $message, array $contacts, array $metadata): void
    {
        try {
            $phoneNumber = $message['from'] ?? null;
            $messageType = $message['type'] ?? null;
            $messageBody = $this->extractMessageText($message);

            if (!$phoneNumber || !$messageBody) {
                return;
            }

            Log::info('Processing WhatsApp message', [
                'from' => $phoneNumber,
                'type' => $messageType,
                'body' => $messageBody
            ]);

            // Identify the contact
            $contact = $this->identifyContact($phoneNumber, $contacts);

            // Process message based on content
            $this->processMessageContent($phoneNumber, $messageBody, $contact);

        } catch (\Exception $e) {
            Log::error('Error handling WhatsApp message', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
        }
    }

    /**
     * Extract text from various message types
     */
    protected function extractMessageText(array $message): ?string
    {
        $type = $message['type'] ?? null;

        switch ($type) {
            case 'text':
                return $message['text']['body'] ?? null;
            case 'button':
                return $message['button']['text'] ?? null;
            case 'interactive':
                if (isset($message['interactive']['button_reply'])) {
                    return $message['interactive']['button_reply']['title'] ?? null;
                }
                if (isset($message['interactive']['list_reply'])) {
                    return $message['interactive']['list_reply']['title'] ?? null;
                }
                break;
            default:
                return null;
        }

        return null;
    }

    /**
     * Identify contact from phone number
     */
    protected function identifyContact(string $phoneNumber, array $contacts): ?array
    {
        foreach ($contacts as $contact) {
            if (($contact['wa_id'] ?? null) === $phoneNumber) {
                return $contact;
            }
        }
        return null;
    }

    /**
     * Process message content and respond appropriately
     */
    protected function processMessageContent(string $phoneNumber, string $messageBody, ?array $contact): void
    {
        $normalizedMessage = strtolower(trim($messageBody));

        // Check for property inquiry patterns
        if ($this->isPropertyInquiry($normalizedMessage)) {
            $this->handlePropertyInquiry($phoneNumber, $messageBody, $contact);
        }
        // Check for viewing schedule requests
        elseif ($this->isViewingRequest($normalizedMessage)) {
            $this->handleViewingRequest($phoneNumber, $messageBody, $contact);
        }
        // Check for general help requests
        elseif ($this->isHelpRequest($normalizedMessage)) {
            $this->sendHelpMessage($phoneNumber);
        }
        // Default response for unrecognized messages
        else {
            $this->sendDefaultResponse($phoneNumber);
        }
    }

    /**
     * Check if message is a property inquiry
     */
    protected function isPropertyInquiry(string $message): bool
    {
        $inquiryKeywords = [
            'property', 'house', 'apartment', 'rent', 'sale', 'available',
            'interested', 'details', 'price', 'cost', 'location'
        ];

        foreach ($inquiryKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if message is a viewing request
     */
    protected function isViewingRequest(string $message): bool
    {
        $viewingKeywords = [
            'viewing', 'visit', 'see', 'inspect', 'tour', 'appointment',
            'schedule', 'book', 'when can i', 'available to view'
        ];

        foreach ($viewingKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if message is a help request
     */
    protected function isHelpRequest(string $message): bool
    {
        $helpKeywords = ['help', 'support', 'how', 'what', 'menu', 'options'];

        foreach ($helpKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle property inquiry
     */
    protected function handlePropertyInquiry(string $phoneNumber, string $messageBody, ?array $contact): void
    {
        $response = "🏠 *Thank you for your property inquiry!*\n\n";
        $response .= "I'd be happy to help you find the perfect property. To provide you with the best recommendations, could you please tell me:\n\n";
        $response .= "📍 *Preferred location/area*\n";
        $response .= "🏠 *Property type* (apartment, house, commercial)\n";
        $response .= "💰 *Budget range*\n";
        $response .= "🛏️ *Number of bedrooms*\n\n";
        $response .= "You can also browse our available properties at: " . url('/properties') . "\n\n";
        $response .= "Our agent will contact you within 2 hours! 😊";

        $this->sendMessage($phoneNumber, $response);

        // Log the inquiry
        $this->logPropertyInquiry($phoneNumber, $messageBody, $contact);
    }

    /**
     * Handle viewing request
     */
    protected function handleViewingRequest(string $phoneNumber, string $messageBody, ?array $contact): void
    {
        $response = "📅 *Property Viewing Request*\n\n";
        $response .= "Great! I'd love to schedule a viewing for you.\n\n";
        $response .= "Please provide:\n";
        $response .= "🏠 *Property address or ID*\n";
        $response .= "📅 *Preferred date and time*\n";
        $response .= "👥 *Number of people attending*\n\n";
        $response .= "Our available viewing slots:\n";
        $response .= "• Monday - Friday: 9:00 AM - 6:00 PM\n";
        $response .= "• Saturday: 10:00 AM - 4:00 PM\n";
        $response .= "• Sunday: By appointment only\n\n";
        $response .= "We'll confirm your appointment within 1 hour! ✅";

        $this->sendMessage($phoneNumber, $response);

        // Log the viewing request
        $this->logViewingRequest($phoneNumber, $messageBody, $contact);
    }

    /**
     * Send help message
     */
    protected function sendHelpMessage(string $phoneNumber): void
    {
        $response = "🤖 *HomeBaze WhatsApp Assistant*\n\n";
        $response .= "I can help you with:\n\n";
        $response .= "🏠 *Property Inquiries* - Find your perfect home\n";
        $response .= "📅 *Schedule Viewings* - Book property tours\n";
        $response .= "💰 *Get Price Information* - Property costs\n";
        $response .= "�� *Location Details* - Area information\n";
        $response .= "📞 *Contact Agents* - Connect with our team\n\n";
        $response .= "Just type your request naturally, and I'll assist you!\n\n";
        $response .= "🌐 Visit our website: " . url('/') . "\n";
        $response .= "📱 Download our app: [Coming Soon]";

        $this->sendMessage($phoneNumber, $response);
    }

    /**
     * Send default response
     */
    protected function sendDefaultResponse(string $phoneNumber): void
    {
        $response = "👋 *Hello from HomeBaze!*\n\n";
        $response .= "Thank you for contacting us. How can I help you today?\n\n";
        $response .= "Type:\n";
        $response .= "• 'Properties' to browse available listings\n";
        $response .= "• 'Schedule viewing' to book a property tour\n";
        $response .= "• 'Help' for assistance\n\n";
        $response .= "Our team is here to help you find your perfect home! 🏠";

        $this->sendMessage($phoneNumber, $response);
    }

    /**
     * Send message via WhatsApp
     */
    protected function sendMessage(string $phoneNumber, string $message): void
    {
        try {
            // Create a dummy invitation object for the existing service
            $fakeInvitation = new class {
                public $phone;
                public function __construct($phone) {
                    $this->phone = $phone;
                }
            };
            $fakeInvitation->phone = $phoneNumber;

            // Use existing WhatsApp service method (we'll enhance this later)
            $this->whatsappService->sendInvitation($fakeInvitation);

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp response', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log property inquiry
     */
    protected function logPropertyInquiry(string $phoneNumber, string $message, ?array $contact): void
    {
        try {
            PropertyInquiry::create([
                'property_id' => null, // General inquiry
                'name' => $contact['profile']['name'] ?? 'WhatsApp User',
                'email' => null,
                'phone' => $phoneNumber,
                'message' => $message,
                'source' => 'whatsapp',
                'status' => 'new'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log property inquiry', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log viewing request
     */
    protected function logViewingRequest(string $phoneNumber, string $message, ?array $contact): void
    {
        try {
            // Log as property inquiry with viewing type for now
            PropertyInquiry::create([
                'property_id' => null,
                'name' => $contact['profile']['name'] ?? 'WhatsApp User',
                'email' => null,
                'phone' => $phoneNumber,
                'message' => 'VIEWING REQUEST: ' . $message,
                'source' => 'whatsapp',
                'status' => 'new'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log viewing request', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }
}