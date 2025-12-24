<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\PropertyInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyInquiryReceived extends Notification implements ShouldQueue
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
            ->subject('New property inquiry received')
            ->greeting('Hello!')
            ->line("You have received a new inquiry for {$this->property->title}.")
            ->line("Name: {$this->inquiry->inquirer_name}")
            ->line("Email: {$this->inquiry->inquirer_email}")
            ->line("Phone: {$this->inquiry->inquirer_phone}")
            ->line('Message:')
            ->line($this->inquiry->message)
            ->action('View property listing', route('property.show', $this->property->slug))
            ->line('Please respond to the customer as soon as possible.');
    }
}
