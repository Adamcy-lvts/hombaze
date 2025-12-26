<?php

namespace App\Notifications;

use Exception;
use Carbon\Carbon;
use App\Models\SmartSearch;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use NotificationChannels\WhatsApp\Component\Text;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use NotificationChannels\WhatsApp\WhatsAppTextMessage;

class SmartSearchMatch extends Notification implements ShouldQueue
{
    use Queueable;

    protected SmartSearch $smartSearch;
    protected Collection $properties;
    protected ?float $matchScore;
    protected bool $isVipExclusive;
    protected ?Carbon $exclusiveUntil;

    /**
     * Create a new notification instance.
     *
     * @param SmartSearch $smartSearch - The smart search that matched
     * @param Collection $properties - The matching properties
     * @param float|null $matchScore - The match score (if single property)
     * @param bool $isVipExclusive - Whether this is a VIP exclusive notification
     * @param Carbon|null $exclusiveUntil - When the exclusive window ends (VIP only)
     */
    public function __construct(
        SmartSearch $smartSearch,
        Collection $properties,
        ?float $matchScore = null,
        bool $isVipExclusive = false,
        ?Carbon $exclusiveUntil = null
    ) {
        $this->smartSearch = $smartSearch;
        $this->properties = $properties;
        $this->matchScore = $matchScore;
        $this->isVipExclusive = $isVipExclusive;
        $this->exclusiveUntil = $exclusiveUntil;
    }

    /**
     * Get the unique identifier for the notification.
     * This prevents duplicate notifications for the same search and properties.
     */
    public function uniqueId(): string
    {
        $propertyIds = $this->properties->pluck('id')->sort()->implode('-');
        return "smart_search_match_{$this->smartSearch->id}_{$propertyIds}";
    }

    /**
     * Get the notification's delivery channels based on tier.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database']; // Always store in database for in-app notifications

        // Get channels based on tier
        $tierChannels = $this->smartSearch->getNotificationChannels();
        $notificationSettings = $this->smartSearch->notification_settings ?? [];

        // Add email if enabled and tier supports it
        if (in_array('email', $tierChannels) && ($notificationSettings['email_alerts'] ?? true) && config('mail.default')) {
            $channels[] = 'mail';
        }

        // Add WhatsApp if enabled, tier supports it, and user has phone number
        if (in_array('whatsapp', $tierChannels) && ($notificationSettings['whatsapp_alerts'] ?? false) && $notifiable->phone && config('services.whatsapp.enabled')) {
            $channels[] = WhatsAppChannel::class;
        }

        // Add SMS if enabled, tier supports it, and user has phone number
        if (in_array('sms', $tierChannels) && ($notificationSettings['sms_alerts'] ?? false) && $notifiable->phone) {
            // $channels[] = 'sms'; // Uncomment when SMS is ready
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $propertyCount = $this->properties->count();
        $searchName = $this->smartSearch->name;
        $tierName = $this->smartSearch->getTierName();

        $subject = $propertyCount === 1
            ? "New Property Match: {$searchName}"
            : "{$propertyCount} New Property Matches: {$searchName}";

        // Add VIP badge to subject for VIP exclusive notifications
        if ($this->isVipExclusive) {
            $subject = "VIP First Dibs - " . $subject;
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! We found {$propertyCount} new " . str('property')->plural($propertyCount) . " matching your {$tierName} SmartSearch '{$searchName}':")
            ->line('');

        // Add VIP exclusive window notice with countdown
        if ($this->isVipExclusive && $this->exclusiveUntil) {
            $hoursRemaining = now()->diffInHours($this->exclusiveUntil);
            $minutesRemaining = now()->diffInMinutes($this->exclusiveUntil) % 60;
            $timeDisplay = $hoursRemaining > 0
                ? "{$hoursRemaining} hour" . ($hoursRemaining > 1 ? 's' : '') . " and {$minutesRemaining} minutes"
                : "{$minutesRemaining} minutes";

            $mail->line("**VIP First Dibs** - You have {$timeDisplay} of exclusive access to view and contact about these properties before other users are notified!")
                 ->line("Exclusive window expires at: " . $this->exclusiveUntil->format('g:i A'))
                 ->line('');
        } elseif ($this->smartSearch->isVip()) {
            $mail->line("**VIP First Dibs** - You have exclusive access to view and contact about these properties before other users are notified!")
                 ->line('');
        }

        foreach ($this->properties->take(5) as $property) { // Limit to 5 in email
            $mail->line("🏠 **{$property->title}**")
                 ->line("📍 {$property->area->name}, {$property->area->city->name}")
                 ->line("💰 {$property->price_with_period}")
                 ->line("🏷️ " . ucfirst($property->listing_type))
                 ->action('View Property', route('property.show', $property->slug))
                 ->line('---');
        }

        if ($this->properties->count() > 5) {
            $remaining = $this->properties->count() - 5;
            $mail->line("...and {$remaining} more " . str('property')->plural($remaining));
        }

        // Main action button - direct to property if single match, otherwise to search page
        $actionText = $propertyCount === 1 ? 'View Property Details' : 'View All Matches';
        $actionUrl = $propertyCount === 1
            ? route('property.show', $this->properties->first()->slug)
            : route('properties.search');

        return $mail
            ->line('Don\'t let these opportunities slip away!')
            ->action($actionText, $actionUrl)
            ->line('You can update your search criteria anytime in your dashboard.')
            ->salutation('Best regards, The HomeBaze Team');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $propertyCount = $this->properties->count();

        $title = $propertyCount === 1
            ? 'New Property Match!'
            : "{$propertyCount} New Property Matches!";

        // Add VIP exclusive prefix
        if ($this->isVipExclusive) {
            $title = 'VIP First Dibs - ' . $title;
        }

        return [
            'type' => 'smart_search_match',
            'unique_id' => $this->uniqueId(),
            'smart_search_id' => $this->smartSearch->id,
            'smart_search_name' => $this->smartSearch->name,
            'tier' => $this->smartSearch->tier,
            'tier_name' => $this->smartSearch->getTierName(),
            'match_score' => $this->matchScore,
            'title' => $title,
            'message' => "Your SmartSearch '{$this->smartSearch->name}' found {$propertyCount} new " .
                        str('property')->plural($propertyCount) . " matching your criteria",
            'property_count' => $propertyCount,
            'is_vip' => $this->smartSearch->isVip(),
            'is_vip_exclusive' => $this->isVipExclusive,
            'exclusive_until' => $this->exclusiveUntil?->toISOString(),
            'exclusive_minutes_remaining' => $this->exclusiveUntil
                ? now()->diffInMinutes($this->exclusiveUntil)
                : null,
            'properties' => $this->properties->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'slug' => $property->slug,
                    'price' => $property->price,
                    'listing_type' => $property->listing_type,
                    'area' => $property->area->name ?? '',
                    'city' => $property->area->city->name ?? '',
                    'image' => $property->getFirstMediaUrl('featured'),
                ];
            })->toArray(),
            'action_url' => $propertyCount === 1
                ? route('property.show', $this->properties->first()->slug)  // Single property details
                : route('properties.search'),  // Multiple properties - search page
            'created_at' => now()->toISOString(),
        ];
    }

    public function toSms($notifiable)
    {
        $count = $this->properties->count();
        $searchName = $this->smartSearch->name;
        $tierName = $this->smartSearch->getTierName();

        $message = $count === 1
            ? "New property match found for your {$tierName} SmartSearch '{$searchName}'! Check your HomeBaze dashboard for details."
            : "{$count} new property matches found for your {$tierName} SmartSearch '{$searchName}'! Check your HomeBaze dashboard for details.";

        // Add VIP exclusive notice with time remaining
        if ($this->isVipExclusive && $this->exclusiveUntil) {
            $hoursRemaining = now()->diffInHours($this->exclusiveUntil);
            $message = "VIP FIRST DIBS: " . $message . " You have {$hoursRemaining}hrs exclusive access!";
        } elseif ($this->smartSearch->isVip()) {
            $message = "VIP FIRST DIBS: " . $message . " You have exclusive access!";
        }

        return $message;
    }

    /**
     * WhatsApp notification using approved template
     */
    public function toWhatsApp($notifiable)
    {
        try {
            $count = $this->properties->count();
            $property = $this->properties->first(); // Get first property for template
            $phoneNumber = $this->formatPhoneNumber($notifiable->phone);

            // Log notification attempt
            Log::info('Attempting to send WhatsApp smart search notification', [
                'user_id' => $notifiable->id,
                'user_name' => $notifiable->name,
                'phone_number' => $phoneNumber,
                'search_id' => $this->smartSearch->id,
                'search_name' => $this->smartSearch->name,
                'tier' => $this->smartSearch->tier,
                'property_count' => $count,
                'property_title' => $property->title ?? 'N/A',
                'template_name' => 'property_match',
                'language' => 'en'
            ]);

            // Create template with body components for each variable
            $template = WhatsAppTemplate::create()
                ->to($phoneNumber)
                ->name('property_match')
                ->language('en');

            // Add body components for each template variable
            $this->addTemplateBodyComponents($template, $count, $property);

            // Log successful template creation
            Log::info('WhatsApp template created successfully for smart search match', [
                'user_id' => $notifiable->id,
                'phone_number' => $phoneNumber,
                'search_id' => $this->smartSearch->id,
                'tier' => $this->smartSearch->tier
            ]);

            return $template;
        } catch (Exception $e) {
            // Log the error but don't fail the entire notification
            Log::warning('WhatsApp template failed for smart search match', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'search_id' => $this->smartSearch->id,
                'search_name' => $this->smartSearch->name,
                'tier' => $this->smartSearch->tier,
                'user_id' => $notifiable->id,
                'user_name' => $notifiable->name,
                'phone_number' => $notifiable->phone ?? 'N/A',
                'template_name' => 'property_match'
            ]);

            // Return null to skip this channel
            return null;
        }
    }

    /**
     * Format WhatsApp message for smart search matches
     */
    private function formatWhatsAppMessage(int $count, string $searchName): string
    {
        $emoji = $count === 1 ? '🏠' : '🎯';
        $title = $count === 1 ? 'New Property Match!' : 'New Property Matches!';
        $tierName = $this->smartSearch->getTierName();

        $message = "{$emoji} *{$title}*\n\n";

        // Add VIP notice
        if ($this->smartSearch->isVip()) {
            $message .= "⭐ *VIP FIRST DIBS* - You have 3 hours exclusive access!\n\n";
        }

        $message .= "Great news! We found {$count} " . Str::plural('property', $count) . " matching your {$tierName} SmartSearch:\n";
        $message .= "🔍 *\"{$searchName}\"*\n\n";

        if ($count === 1) {
            $property = $this->properties->first();
            $message .= "🏠 *{$property->title}*\n";
            $message .= "📍 {$property->area->name}, {$property->area->city->name}\n";
            $message .= "💰 {$property->price_with_period}\n";
            $message .= "🏷️ " . ucfirst($property->listing_type) . "\n\n";
            $message .= "👁️ View Details: " . route('property.show', $property->slug) . "\n\n";
        } else {
            // Show top 3 properties for multiple matches
            foreach ($this->properties->take(3) as $index => $property) {
                $message .= "*" . ($index + 1) . ". {$property->title}*\n";
                $message .= "📍 {$property->area->name}, {$property->area->city->name}\n";
                $message .= "💰 {$property->price_with_period}\n\n";
            }

            if ($count > 3) {
                $remaining = $count - 3;
                $message .= "...and {$remaining} more " . Str::plural('property', $remaining) . "!\n\n";
            }

            $message .= "👁️ View All Matches: " . route('properties.search') . "\n\n";
        }

        $message .= "⚡ Don't let these opportunities slip away!\n";
        $message .= "💬 Reply to this message if you need help or have questions.\n\n";
        $message .= "🔐 *HomeBaze - Your Trusted Property Partner*";

        return $message;
    }

    /**
     * Get formatted property location
     */
    private function getPropertyLocation($property): string
    {
        $location = [];

        if ($property->area && $property->area->name) {
            $location[] = $property->area->name;
        }

        if ($property->area && $property->area->city && $property->area->city->name) {
            $location[] = $property->area->city->name;
        }

        return !empty($location) ? implode(', ', $location) : 'Location not specified';
    }

    /**
     * Add template body components with property data
     */
    private function addTemplateBodyComponents(WhatsAppTemplate $template, int $count, $property): void
    {
        // Map property data to template variables based on the approved template
        // Template variables: {{1}} {{2}} {{3}} {{4}} {{5}} {{6}} {{7}}

        // {{1}} - Property count
        $template->body(new Text($count > 1 ? "{$count} properties" : '1 property'));

        // {{2}} - Property title
        $template->body(new Text($property->title ?? 'Property'));

        // {{3}} - Location/Area
        $template->body(new Text($property->area->name ?? 'Area'));

        // {{4}} - City
        $template->body(new Text($property->area->city->name ?? 'City'));

        // {{5}} - Price with period (no double ₦ symbol)
        $template->body(new Text($property->price_with_period ?? number_format($property->price ?? 0)));

        // {{6}} - Property type
        $template->body(new Text(ucfirst($property->listing_type ?? 'sale')));

        // {{7}} - Property URL for direct access (clickable link in WhatsApp)
        $template->body(new Text(route('property.show', $property->slug ?? '')));
    }

    /**
     * Format phone number for WhatsApp (remove leading + and country code formatting)
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Remove leading + if present
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        // If phone starts with 0, replace with 234 (Nigeria country code)
        if (str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        }

        // If phone doesn't start with 234, assume it's a Nigerian number and add country code
        if (!str_starts_with($phone, '234')) {
            $phone = '234' . $phone;
        }

        return $phone;
    }
}
