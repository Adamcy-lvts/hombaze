<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\PropertyInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyInquiryConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected PropertyInquiry $inquiry,
        protected Property $property
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your property inquiry')
            ->greeting("Hi {$this->inquiry->inquirer_name},")
            ->line("Thanks for your interest in {$this->property->title}. Your inquiry has been sent to the property contact.")
            ->line('Message sent:')
            ->line($this->inquiry->message)
            ->action('View property listing', route('property.show', $this->property->slug))
            ->line('We will notify you when the agent responds.');
    }
}
