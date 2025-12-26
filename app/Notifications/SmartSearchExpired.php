<?php

namespace App\Notifications;

use App\Models\SmartSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SmartSearchExpired extends Notification implements ShouldQueue
{
    use Queueable;

    protected SmartSearch $smartSearch;

    public function __construct(SmartSearch $smartSearch)
    {
        $this->smartSearch = $smartSearch;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('mail.default')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $searchName = $this->smartSearch->name;
        $tierName = $this->smartSearch->getTierName();
        $matchesSent = $this->smartSearch->matches_sent ?? 0;

        return (new MailMessage)
            ->subject("Your SmartSearch '{$searchName}' Has Expired")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your {$tierName} SmartSearch **'{$searchName}'** has now expired.")
            ->line($matchesSent > 0
                ? "During its active period, we found **{$matchesSent}** matching " . str('property')->plural($matchesSent) . " for you."
                : "We searched actively for properties matching your criteria.")
            ->line('')
            ->line('Want to continue your property search?')
            ->action('Get a New SmartSearch', route('smartsearch.pricing'))
            ->line('')
            ->line('Thank you for using SmartSearch - your 24/7 property hunting assistant!')
            ->salutation('Best regards, The HomeBaze Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'smart_search_expired',
            'smart_search_id' => $this->smartSearch->id,
            'smart_search_name' => $this->smartSearch->name,
            'tier' => $this->smartSearch->tier,
            'tier_name' => $this->smartSearch->getTierName(),
            'title' => 'SmartSearch Expired',
            'message' => "Your SmartSearch '{$this->smartSearch->name}' has expired.",
            'matches_sent' => $this->smartSearch->matches_sent ?? 0,
            'action_url' => route('smartsearch.pricing'),
            'created_at' => now()->toISOString(),
        ];
    }
}
