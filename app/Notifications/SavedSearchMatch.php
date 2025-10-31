<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Models\SavedSearch;

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
}