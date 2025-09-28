<?php

namespace App\Services\Communication;

use App\Models\TenantInvitation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class SmsService
{
    protected string $provider;
    protected array $config;
    protected bool $enabled;

    public function __construct()
    {
        $this->provider = config('services.sms.default_provider', 'termii');
        $this->config = config('services.sms.providers', []);
        $this->enabled = config('services.sms.enabled', false);
    }

    /**
     * Send invitation SMS (for future automation)
     */
    public function sendInvitation(TenantInvitation $invitation): array
    {
        if (!$this->enabled) {
            throw new Exception('SMS service is not enabled');
        }

        $message = $this->formatInvitationMessage($invitation);

        try {
            switch ($this->provider) {
                case 'termii':
                    return $this->sendViaTermii($invitation->phone, $message);

                case 'bulk_sms':
                    return $this->sendViaBulkSms($invitation->phone, $message);

                case 'twilio':
                    return $this->sendViaTwilio($invitation->phone, $message);

                default:
                    throw new Exception("Unsupported SMS provider: {$this->provider}");
            }
        } catch (Exception $e) {
            Log::error('Failed to send SMS invitation', [
                'invitation_id' => $invitation->id,
                'phone' => $invitation->phone,
                'provider' => $this->provider,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via Termii (Nigerian provider)
     */
    protected function sendViaTermii(string $phone, string $message): array
    {
        $config = $this->config['termii'] ?? [];

        if (empty($config['api_key'])) {
            throw new Exception('Termii API key not configured');
        }

        $response = Http::post('https://api.ng.termii.com/api/sms/send', [
            'to' => $this->formatPhoneNumber($phone),
            'from' => $config['sender_id'] ?? 'HomeBaze',
            'sms' => $message,
            'type' => 'plain',
            'api_key' => $config['api_key'],
            'channel' => $config['channel'] ?? 'generic'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Log::info('SMS sent via Termii successfully', [
                'phone' => $phone,
                'message_id' => $data['message_id'] ?? null
            ]);

            return [
                'success' => true,
                'provider' => 'termii',
                'message_id' => $data['message_id'] ?? null,
                'response' => $data
            ];
        }

        throw new Exception('Termii API request failed: ' . $response->body());
    }

    /**
     * Send SMS via Bulk SMS Nigeria
     */
    protected function sendViaBulkSms(string $phone, string $message): array
    {
        $config = $this->config['bulk_sms'] ?? [];

        if (empty($config['username']) || empty($config['password'])) {
            throw new Exception('Bulk SMS credentials not configured');
        }

        $response = Http::post('https://www.bulksmsnigeria.com/api/v1/sms/create', [
            'api_token' => $config['api_token'],
            'from' => $config['sender_id'] ?? 'HomeBaze',
            'to' => $this->formatPhoneNumber($phone),
            'body' => $message,
            'dnd' => '2' // Bypass DND
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Log::info('SMS sent via Bulk SMS Nigeria successfully', [
                'phone' => $phone,
                'response' => $data
            ]);

            return [
                'success' => true,
                'provider' => 'bulk_sms',
                'response' => $data
            ];
        }

        throw new Exception('Bulk SMS API request failed: ' . $response->body());
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $phone, string $message): array
    {
        $config = $this->config['twilio'] ?? [];

        if (empty($config['account_sid']) || empty($config['auth_token'])) {
            throw new Exception('Twilio credentials not configured');
        }

        $response = Http::withBasicAuth($config['account_sid'], $config['auth_token'])
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$config['account_sid']}/Messages.json", [
                'From' => $config['from_number'],
                'To' => $this->formatPhoneNumber($phone, true), // Twilio needs + prefix
                'Body' => $message
            ]);

        if ($response->successful()) {
            $data = $response->json();

            Log::info('SMS sent via Twilio successfully', [
                'phone' => $phone,
                'sid' => $data['sid'] ?? null
            ]);

            return [
                'success' => true,
                'provider' => 'twilio',
                'message_id' => $data['sid'] ?? null,
                'response' => $data
            ];
        }

        throw new Exception('Twilio API request failed: ' . $response->body());
    }

    /**
     * Format invitation message for SMS
     */
    protected function formatInvitationMessage(TenantInvitation $invitation): string
    {
        $landlordName = $invitation->landlord->name;
        $propertyTitle = $invitation->property ? $invitation->property->title : 'their property';
        $invitationUrl = $invitation->getInvitationUrl();

        $message = "🏠 HomeBaze Invitation\n\n";
        $message .= "Hi! {$landlordName} invited you as tenant";

        if ($invitation->property) {
            $message .= " for {$propertyTitle}";
        }

        $message .= ".\n\n";
        $message .= "Complete registration: {$invitationUrl}\n\n";
        $message .= "Valid until {$invitation->expires_at->format('M j, Y')}";

        return $message;
    }

    /**
     * Format phone number for SMS providers
     */
    protected function formatPhoneNumber(string $phone, bool $withPlus = false): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If phone starts with +234, handle based on withPlus parameter
        if (str_starts_with($phone, '+234')) {
            return $withPlus ? $phone : substr($phone, 1);
        }

        // If phone starts with 234, add + if needed
        if (str_starts_with($phone, '234')) {
            return $withPlus ? '+' . $phone : $phone;
        }

        // If phone starts with 0, replace with 234
        if (str_starts_with($phone, '0')) {
            $formatted = '234' . substr($phone, 1);
            return $withPlus ? '+' . $formatted : $formatted;
        }

        // Otherwise assume it's a Nigerian number without country code
        $formatted = '234' . $phone;
        return $withPlus ? '+' . $formatted : $formatted;
    }

    /**
     * Track SMS sending
     */
    public function trackSending(TenantInvitation $invitation): void
    {
        $sentVia = $invitation->sent_via ?? [];
        $sentVia[] = [
            'method' => 'sms',
            'provider' => $this->provider,
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
     * Check if SMS service is available
     */
    public function isAvailable(): bool
    {
        return $this->enabled && !empty($this->config[$this->provider]);
    }

    /**
     * Get service status for admin dashboard
     */
    public function getStatus(): array
    {
        $providerConfig = $this->config[$this->provider] ?? [];

        return [
            'enabled' => $this->enabled,
            'provider' => $this->provider,
            'configured' => !empty($providerConfig),
            'available_providers' => array_keys($this->config)
        ];
    }
}