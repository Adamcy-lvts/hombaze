<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class PropertyRecommendation extends Notification implements ShouldQueue
{
    use Queueable;

    protected Collection $properties;

    public function __construct(Collection $properties)
    {
        $this->properties = $properties;
    }

    public function via($notifiable)
    {
        $channels = ['mail'];

        // Add SMS if user has enabled it and has phone number
        if ($notifiable->customerProfile?->sms_alerts && $notifiable->phone) {
            $channels[] = 'sms';
        }

        // Add database notification for in-app notifications
        $channels[] = 'database';

        return $channels;
    }

    public function toMail($notifiable)
    {
        $propertyCount = $this->properties->count();
        $subject = $propertyCount === 1
            ? 'New Property Match Found!'
            : "{$propertyCount} New Property Matches Found!";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("We found {$propertyCount} new " . str('property')->plural($propertyCount) . " that match your preferences:")
            ->line(''); // Empty line for spacing

        foreach ($this->properties as $property) {
            $mail->line("🏠 **{$property->title}**")
                 ->line("📍 {$property->area->name}, {$property->area->city->name}")
                 ->line("💰 ₦" . number_format($property->price))
                 ->line("🏷️ {$property->listing_type}")
                 ->action('View Property', route('property.show', $property->slug))
                 ->line('---'); // Separator
        }

        return $mail
            ->line('Don\'t miss out on these opportunities!')
            ->action('View All Properties', route('properties.search'))
            ->line('You can update your preferences anytime in your dashboard.')
            ->salutation('Best regards, The HomeBaze Team');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'property_recommendation',
            'title' => $this->properties->count() === 1
                ? 'New Property Match!'
                : $this->properties->count() . ' New Property Matches!',
            'message' => "We found {$this->properties->count()} new " .
                        str('property')->plural($this->properties->count()) .
                        " matching your preferences",
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
            'action_url' => route('properties.search'),
        ];
    }

    public function toSms($notifiable)
    {
        $count = $this->properties->count();
        $message = $count === 1
            ? "New property match found! Check your HomeBaze dashboard to view details."
            : "{$count} new property matches found! Check your HomeBaze dashboard to view details.";

        return $message;
    }
}