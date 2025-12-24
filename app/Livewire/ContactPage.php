<?php

namespace App\Livewire;

use App\Models\State;
use App\Models\City;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ContactPage extends Component
{
    #[Validate('required|min:2')]
    public $name = '';

    #[Validate('required|email')]
    public $email = '';

    #[Validate('required|min:10')]
    public $phone = '';

    #[Validate('required')]
    public $subject = '';

    #[Validate('required|min:10')]
    public $message = '';

    public $inquiry_type = 'general';

    public $success = false;

    public function submit()
    {
        $this->validate();

        // Here you would typically save to database or send email
        // For now, we'll just simulate success
        
        // Example: Save to database
        // ContactInquiry::create([
        //     'name' => $this->name,
        //     'email' => $this->email,
        //     'phone' => $this->phone,
        //     'subject' => $this->subject,
        //     'message' => $this->message,
        //     'inquiry_type' => $this->inquiry_type,
        // ]);

        // Reset form
        $this->reset(['name', 'email', 'phone', 'subject', 'message', 'inquiry_type']);
        $this->success = true;

        // Auto-hide success message after 5 seconds
        $this->dispatch('hide-success-message');
    }

    public function getOfficeLocationsProperty()
    {
        return [
            [
                'city' => 'Lagos',
                'address' => 'Plot 123, Victoria Island, Lagos State',
                'phone' => '+234 (0) 1 234 5678',
                'email' => 'lagos@homebaze.ng',
                'hours' => 'Mon - Fri: 8:00 AM - 6:00 PM, Sat: 9:00 AM - 4:00 PM'
            ],
            [
                'city' => 'Abuja',
                'address' => 'Suite 456, Central Business District, Abuja FCT',
                'phone' => '+234 (0) 9 234 5679',
                'email' => 'abuja@homebaze.ng',
                'hours' => 'Mon - Fri: 8:00 AM - 6:00 PM, Sat: 9:00 AM - 4:00 PM'
            ],
            [
                'city' => 'Port Harcourt',
                'address' => 'Block 789, GRA Phase 2, Port Harcourt, Rivers State',
                'phone' => '+234 (0) 84 234 5680',
                'email' => 'portharcourt@homebaze.ng',
                'hours' => 'Mon - Fri: 8:00 AM - 6:00 PM, Sat: 9:00 AM - 4:00 PM'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.contact-page', [
            'officeLocations' => $this->officeLocations,
        ])->layout('layouts.guest-app', ['title' => 'Contact HomeBaze - Get In Touch']);
    }
}