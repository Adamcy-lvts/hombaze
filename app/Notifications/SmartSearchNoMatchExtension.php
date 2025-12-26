<?php

namespace App\Notifications;

use App\Models\SmartSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SmartSearchNoMatchExtension extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("Free 30-Day Extension for Your SmartSearch '{$searchName}'")
            ->greeting("Hello {$notifiable->name}!")
            ->line("We noticed your {$tierName} SmartSearch **'{$searchName}'** expired without finding any matches.")
            ->line('')
            ->line("**We're sorry we couldn't find what you're looking for!**")
            ->line('')
            ->line("As a goodwill gesture, we'd like to offer you a **FREE 30-day extension** so we can keep searching for your perfect property.")
            ->line('')
            ->line('Simply click the button below to activate your extension:')
            ->action('Get My Free Extension', route('customer.searches.index'))
            ->line('')
            ->line('**Tips for better matches:**')
            ->line('- Consider expanding your location preferences')
            ->line('- Adjust your budget range slightly')
            ->line('- Try different property types')
            ->line('')
            ->line('We\'ll keep hunting 24/7 until we find the right property for you!')
            ->salutation('Best regards, The HomeBaze Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'smart_search_no_match_extension',
            'smart_search_id' => $this->smartSearch->id,
            'smart_search_name' => $this->smartSearch->name,
            'tier' => $this->smartSearch->tier,
            'tier_name' => $this->smartSearch->getTierName(),
            'title' => 'Free 30-Day Extension Available',
            'message' => "Your SmartSearch '{$this->smartSearch->name}' found no matches. Claim your free 30-day extension!",
            'can_extend' => true,
            'extension_days' => 30,
            'action_url' => route('customer.searches.index'),
            'created_at' => now()->toISOString(),
        ];
    }
}
