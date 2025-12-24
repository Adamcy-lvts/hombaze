<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Settings extends Component
{
    // Notification Settings
    public $email_notifications = true;
    public $new_properties = true;
    public $price_alerts = true;
    public $inquiry_responses = true;
    public $marketing_emails = false;

    public function mount()
    {
        $user = auth()->user();

        $this->email_notifications = $user->email_notifications ?? true;
        $this->new_properties = $user->new_properties ?? true;
        $this->price_alerts = $user->price_alerts ?? true;
        $this->inquiry_responses = $user->inquiry_responses ?? true;
        $this->marketing_emails = $user->marketing_emails ?? false;
    }

    public function updateNotifications()
    {
        auth()->user()->update([
            'email_notifications' => $this->email_notifications,
            'new_properties' => $this->new_properties,
            'price_alerts' => $this->price_alerts,
            'inquiry_responses' => $this->inquiry_responses,
            'marketing_emails' => $this->marketing_emails,
        ]);

        session()->flash('success', 'Notification preferences updated successfully!');
    }

    public function render()
    {
        return view('livewire.customer.settings')->layout('layouts.guest-app', [
            'title' => 'Account Settings - HomeBaze',
            'hideNav' => true,
        ]);
    }
}
