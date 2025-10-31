<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $showDropdown = false;
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Get unread notifications
        $this->notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;

        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        if (!auth()->check()) {
            return;
        }

        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications(); // Refresh the list

            // If this is a SavedSearchMatch notification, redirect to the property
            if ($notification->type === 'App\\Notifications\\SavedSearchMatch') {
                $actionUrl = $notification->data['action_url'] ?? null;
                if ($actionUrl) {
                    return redirect($actionUrl);
                }
            }
        }
    }

    public function markAllAsRead()
    {
        if (!auth()->check()) {
            return;
        }

        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications(); // Refresh the list
    }

    #[On('notification-received')]
    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
