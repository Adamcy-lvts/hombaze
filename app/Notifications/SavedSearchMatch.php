<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Models\SavedSearch;
use NotificationChannels\WhatsApp\WhatsAppChannel;
use NotificationChannels\WhatsApp\WhatsAppTextMessage;
use NotificationChannels\WhatsApp\WhatsAppTemplate;
use NotificationChannels\WhatsApp\Component\Text;

class SavedSearchMatch extends Notification implements ShouldQueue
{
    use Queueable;

    protected SavedSearch $savedSearch;
    protected Collection $properties;
    protected ?float $matchScore;

    public function __construct(SavedSearch $savedSearch, Collection $properties, ?float $matchScore = null)
    {
        $this->savedSearch = $savedSearch;
        $this->properties = $properties;
        $this->matchScore = $matchScore;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database']; // Always store in database for in-app notifications

        $notificationSettings = $this->savedSearch->notification_settings ?? [];

        // Add email if enabled (when email channel is properly configured)
        if (($notificationSettings['email_alerts'] ?? false) && config('mail.default')) {
            // $channels[] = 'mail'; // Uncomment when email is ready
        }

        // Add WhatsApp if enabled and user has phone number
        if (($notificationSettings['whatsapp_alerts'] ?? false) && $notifiable->phone && config('services.whatsapp.enabled')) {
            $channels[] = WhatsAppChannel::class;
        }

        // Add SMS if enabled and user has phone number (when SMS channel is configured)
        if (($notificationSettings['sms_alerts'] ?? false) && $notifiable->phone) {
            // $channels[] = 'sms'; // Uncomment when SMS is ready
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $propertyCount = $this->properties->count();
        $searchName = $this->savedSearch->name;

        $subject = $propertyCount === 1
            ? "New Property Match: {$searchName}"
            : "{$propertyCount} New Property Matches: {$searchName}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! We found {$propertyCount} new " . str('property')->plural($propertyCount) . " matching your saved search '{$searchName}':")
            ->line('');

        foreach ($this->properties->take(5) as $property) { // Limit to 5 in email
            $mail->line("🏠 **{$property->title}**")
                 ->line("📍 {$property->area->name}, {$property->area->city->name}")
                 ->line("💰 ₦" . number_format($property->price))
                 ->line("🏷️ " . ucfirst($property->listing_type))
                 ->action('View Property', route('property.show', $property->slug))
                 ->line('---');
        }

        if ($this->properties->count() > 5) {
            $remaining = $this->properties->count() - 5;
            $mail->line("...and {$remaining} more " . str('property')->plural($remaining));
        }

        return $mail
            ->line('Don\'t let these opportunities slip away!')
            ->action('View All Matches', route('customer.searches.show', $this->savedSearch->id))
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

        return [
            'type' => 'saved_search_match',
            'saved_search_id' => $this->savedSearch->id,
            'saved_search_name' => $this->savedSearch->name,
            'match_score' => $this->matchScore,
            'title' => $propertyCount === 1
                ? 'New Property Match!'
                : "{$propertyCount} New Property Matches!",
            'message' => "Your saved search '{$this->savedSearch->name}' found {$propertyCount} new " .
                        str('property')->plural($propertyCount) . " matching your criteria",
            'property_count' => $propertyCount,
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
        $searchName = $this->savedSearch->name;

        $message = $count === 1
            ? "New property match found for '{$searchName}'! Check your HomeBaze dashboard for details."
            : "{$count} new property matches found for '{$searchName}'! Check your HomeBaze dashboard for details.";

        return $message;
    }

    /**
     * WhatsApp notification using approved template
     */
    public function toWhatsApp($notifiable)
    {
        $count = $this->properties->count();
        $property = $this->properties->first(); // Get first property for template

        // Create template with body components for each variable
        $template = WhatsAppTemplate::create()
            ->to($this->formatPhoneNumber($notifiable->phone))
            ->name('property_match')
            ->language('en_US');

        // Add body components for each template variable
        $this->addTemplateBodyComponents($template, $count, $property);

        return $template;
    }

    /**
     * Format WhatsApp message for saved search matches
     */
    private function formatWhatsAppMessage(int $count, string $searchName): string
    {
        $emoji = $count === 1 ? '🏠' : '🎯';
        $title = $count === 1 ? 'New Property Match!' : 'New Property Matches!';

        $message = "{$emoji} *{$title}*\n\n";
        $message .= "Great news! We found {$count} " . Str::plural('property', $count) . " matching your saved search:\n";
        $message .= "🔍 *\"{$searchName}\"*\n\n";

        if ($count === 1) {
            $property = $this->properties->first();
            $message .= "🏠 *{$property->title}*\n";
            $message .= "📍 {$property->area->name}, {$property->area->city->name}\n";
            $message .= "💰 ₦" . number_format($property->price) . "\n";
            $message .= "🏷️ " . ucfirst($property->listing_type) . "\n\n";
            $message .= "👁️ View Details: " . route('property.show', $property->slug) . "\n\n";
        } else {
            // Show top 3 properties for multiple matches
            foreach ($this->properties->take(3) as $index => $property) {
                $message .= "*" . ($index + 1) . ". {$property->title}*\n";
                $message .= "📍 {$property->area->name}, {$property->area->city->name}\n";
                $message .= "💰 ₦" . number_format($property->price) . "\n\n";
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
        // Template variables: {{1}} {{2}} {{3}} {{4}} {{5}} {{6}}

        // {{1}} - Property count
        $template->body(new Text($count > 1 ? "{$count} properties" : '1 property'));

        // {{2}} - Property title
        $template->body(new Text($property->title ?? 'Property'));

        // {{3}} - Location/Area
        $template->body(new Text($property->area->name ?? 'Area'));

        // {{4}} - City
        $template->body(new Text($property->area->city->name ?? 'City'));

        // {{5}} - Price
        $template->body(new Text('₦' . number_format($property->price ?? 0)));

        // {{6}} - Listing type
        $template->body(new Text(ucfirst($property->listing_type ?? 'sale')));
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