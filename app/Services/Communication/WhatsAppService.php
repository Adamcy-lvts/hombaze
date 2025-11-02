<?php

namespace App\Services\Communication;

use App\Models\TenantInvitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class WhatsAppService
{
    protected string $apiUrl;
    protected ?string $accessToken;
    protected ?string $phoneNumberId;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * Generate WhatsApp share link for tenant invitation
     */
    public function generateInvitationShareLink(TenantInvitation $invitation): string
    {
        $message = $this->formatInvitationMessage($invitation);
        $encodedMessage = urlencode($message);

        // WhatsApp share link format
        return "https://wa.me/{$invitation->phone}?text={$encodedMessage}";
    }

    /**
     * Send invitation message via WhatsApp Business API (for future use)
     */
    public function sendInvitation(TenantInvitation $invitation): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        if (!$this->accessToken || !$this->phoneNumberId) {
            throw new Exception('WhatsApp credentials not configured');
        }

        $message = $this->formatInvitationMessage($invitation);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($invitation->phone),
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp invitation sent successfully', [
                    'invitation_id' => $invitation->id,
                    'phone' => $invitation->phone,
                    'message_id' => $data['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data
                ];
            }

            throw new Exception('WhatsApp API request failed: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp invitation', [
                'invitation_id' => $invitation->id,
                'phone' => $invitation->phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format invitation message for WhatsApp
     */
    protected function formatInvitationMessage(TenantInvitation $invitation): string
    {
        $landlordName = $invitation->landlord->name;
        $propertyTitle = $invitation->property ? $invitation->property->title : 'their property';
        $invitationUrl = $invitation->getInvitationUrl();

        $message = "🏠 *HomeBaze Tenant Invitation*\n\n";
        $message .= "Hi! You've been invited by *{$landlordName}* to join HomeBaze as a tenant";

        if ($invitation->property) {
            $message .= " for *{$propertyTitle}*";
        }

        $message .= ".\n\n";

        if ($invitation->message) {
            $message .= "💬 *Personal Message:*\n\"{$invitation->message}\"\n\n";
        }

        $message .= "📱 *Complete your registration here:*\n{$invitationUrl}\n\n";
        $message .= "⏰ This invitation expires on {$invitation->expires_at->format('M j, Y \\a\\t g:i A')}\n\n";
        $message .= "🔐 Powered by HomeBaze - Nigeria's Premier Real Estate Platform";

        return $message;
    }

    /**
     * Format phone number for WhatsApp API
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If phone starts with +234, keep as is
        if (str_starts_with($phone, '+234')) {
            return substr($phone, 1); // Remove + for API
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

    /**
     * Track invitation sharing
     */
    public function trackSharing(TenantInvitation $invitation, string $method = 'whatsapp'): void
    {
        $sentVia = $invitation->sent_via ?? [];
        $sentVia[] = [
            'method' => $method,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip()
        ];

        $invitation->update([
            'sent_via' => $sentVia,
            'link_copied_at' => now(),
            'link_copy_count' => $invitation->link_copy_count + 1
        ]);
    }

    /**
     * Check if WhatsApp service is available
     */
    public function isAvailable(): bool
    {
        return $this->enabled && !empty($this->accessToken) && !empty($this->phoneNumberId);
    }

    /**
     * Get service status for admin dashboard
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'configured' => !empty($this->accessToken) && !empty($this->phoneNumberId),
            'api_url' => $this->apiUrl,
            'phone_number_id' => $this->phoneNumberId ? 'Set' : 'Not Set',
            'access_token' => $this->accessToken ? 'Set' : 'Not Set'
        ];
    }

    /**
     * Send property inquiry message with optional image
     */
    public function sendPropertyInquiry(string $phoneNumber, string $propertyTitle, string $propertyUrl, array $propertyDetails = [], string $imageUrl = null): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        // If image URL is provided, send as media message first, then text
        if ($imageUrl) {
            $imageResult = $this->sendImageMessage($phoneNumber, $imageUrl, $propertyTitle);

            // Wait a bit before sending text message
            usleep(500000); // 0.5 seconds

            $message = $this->formatPropertyInquiryMessage($propertyTitle, $propertyUrl, $propertyDetails, true);
            $textResult = $this->sendTextMessage($phoneNumber, $message);

            return [
                'success' => $imageResult['success'] && $textResult['success'],
                'image_result' => $imageResult,
                'text_result' => $textResult
            ];
        }

        $message = $this->formatPropertyInquiryMessage($propertyTitle, $propertyUrl, $propertyDetails);
        return $this->sendTextMessage($phoneNumber, $message);
    }

    /**
     * Send viewing confirmation message
     */
    public function sendViewingConfirmation(string $phoneNumber, array $viewingDetails): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        $message = $this->formatViewingConfirmationMessage($viewingDetails);

        return $this->sendTextMessage($phoneNumber, $message);
    }

    /**
     * Send general text message
     */
    public function sendTextMessage(string $phoneNumber, string $message): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        if (!$this->accessToken || !$this->phoneNumberId) {
            throw new Exception('WhatsApp credentials not configured');
        }

        try {
            // In development mode, use template instead of free-form text
            if (config('app.env') !== 'production') {
                return $this->sendTemplateMessage($phoneNumber, 'hello_world');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($phoneNumber),
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phoneNumber,
                    'message_id' => $data['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data
                ];
            }

            throw new Exception('WhatsApp API request failed: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send template message
     */
    public function sendTemplateMessage(string $phoneNumber, string $templateName, array $parameters = [], string $languageCode = 'en_US'): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        if (!$this->accessToken || !$this->phoneNumberId) {
            throw new Exception('WhatsApp credentials not configured');
        }

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($phoneNumber),
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode
                    ]
                ]
            ];

            // Add parameters if provided
            if (!empty($parameters)) {
                $payload['template']['components'] = [
                    [
                        'type' => 'body',
                        'parameters' => $parameters
                    ]
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp template message sent successfully', [
                    'phone' => $phoneNumber,
                    'template' => $templateName,
                    'message_id' => $data['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data
                ];
            }

            throw new Exception('WhatsApp API request failed: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp template message', [
                'phone' => $phoneNumber,
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send property match notification using custom template
     */
    public function sendPropertyMatchNotification(string $phoneNumber, array $propertyData): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        try {
            // Format the parameters for our custom template
            $parameters = [
                ['type' => 'text', 'text' => $propertyData['property_count']], // {{1}} - "1 property" or "2 properties"
                ['type' => 'text', 'text' => $propertyData['search_name']],    // {{2}} - Saved search name
                ['type' => 'text', 'text' => $propertyData['property_title']], // {{3}} - Property title
                ['type' => 'text', 'text' => $propertyData['location']],       // {{4}} - Location
                ['type' => 'text', 'text' => $propertyData['price']],          // {{5}} - Price (number only)
                ['type' => 'text', 'text' => $propertyData['property_type']],  // {{6}} - Property type
                ['type' => 'text', 'text' => $propertyData['url']]             // {{7}} - Property URL
            ];

            // Use simple template until custom template is approved
            if (empty($parameters) || config('app.env') !== 'production') {
                $fallbackTemplate = config('services.whatsapp.templates.fallback', 'hello_world');
                return $this->sendTemplateMessage($phoneNumber, $fallbackTemplate);
            }

            $propertyTemplate = config('services.whatsapp.templates.property_match', 'property_match');
            return $this->sendTemplateMessage($phoneNumber, $propertyTemplate, $parameters);

        } catch (Exception $e) {
            Log::error('Failed to send property match notification', [
                'phone' => $phoneNumber,
                'property_data' => $propertyData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send interactive button message
     */
    public function sendButtonMessage(string $phoneNumber, string $bodyText, array $buttons): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($phoneNumber),
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'button',
                    'body' => ['text' => $bodyText],
                    'action' => [
                        'buttons' => $buttons
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data
                ];
            }

            throw new Exception('WhatsApp API request failed: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp button message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send image message
     */
    public function sendImageMessage(string $phoneNumber, string $imageUrl, string $caption = ''): array
    {
        if (!$this->enabled) {
            throw new Exception('WhatsApp service is not enabled');
        }

        if (!$this->accessToken || !$this->phoneNumberId) {
            throw new Exception('WhatsApp credentials not configured');
        }

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($phoneNumber),
                'type' => 'image',
                'image' => [
                    'link' => $imageUrl
                ]
            ];

            // Add caption if provided
            if (!empty($caption)) {
                $payload['image']['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp image sent successfully', [
                    'phone' => $phoneNumber,
                    'image_url' => $imageUrl,
                    'message_id' => $data['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'response' => $data
                ];
            }

            throw new Exception('WhatsApp API request failed: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp image', [
                'phone' => $phoneNumber,
                'image_url' => $imageUrl,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format property inquiry message
     */
    protected function formatPropertyInquiryMessage(string $propertyTitle, string $propertyUrl, array $details, bool $imageAlreadySent = false): string
    {
        $message = "🏠 *Property Inquiry - HomeBaze*\n\n";
        $message .= $imageAlreadySent ?
            "Property: *{$propertyTitle}*\n\n" :
            "Thank you for your interest in: *{$propertyTitle}*\n\n";

        if (!empty($details)) {
            $message .= "📋 *Property Details:*\n";
            foreach ($details as $key => $value) {
                $message .= "• {$key}: {$value}\n";
            }
            $message .= "\n";
        }

        $message .= "🔗 *View Full Details:* {$propertyUrl}\n\n";
        $message .= "Please let me know if it's still available and if I can schedule a viewing. Thank you! 😊\n\n";
        $message .= "🔐 *Via HomeBaze - Nigeria's Premier Real Estate Platform*";

        return $message;
    }

    /**
     * Format viewing confirmation message
     */
    protected function formatViewingConfirmationMessage(array $details): string
    {
        $message = "✅ *Viewing Appointment Confirmed - HomeBaze*\n\n";
        $message .= "📅 *Date & Time:* {$details['date']} at {$details['time']}\n";
        $message .= "🏠 *Property:* {$details['property_title']}\n";
        $message .= "📍 *Location:* {$details['address']}\n";
        $message .= "👤 *Agent:* {$details['agent_name']}\n";
        $message .= "📞 *Agent Contact:* {$details['agent_phone']}\n\n";

        $message .= "📋 *What to bring:*\n";
        $message .= "• Valid ID\n";
        $message .= "• Proof of income (if interested)\n";
        $message .= "• Any questions you have\n\n";

        $message .= "⚠️ *Please arrive 5 minutes early*\n\n";
        $message .= "Need to reschedule? Reply to this message or call our agent directly.\n\n";
        $message .= "🔐 *HomeBaze - Your Trusted Property Partner*";

        return $message;
    }
}