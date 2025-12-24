<?php

namespace App\Livewire;

use Livewire\Component;

class TestComponent extends Component
{
    public function render()
    {
        return view('livewire.test-component')
            ->layout('layouts.guest-app')
            ->section('content')
            ->title('Test Page');
    }
}
