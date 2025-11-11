<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\ViewField;

class FeaturedImageUploadField
{
    public static function make(string $name): ViewField
    {
        return ViewField::make($name)
            ->view('filament.forms.components.featured-image-upload-field')
            ->afterStateHydrated(function (ViewField $component, $state, $record) {
                if ($record) {
                    $featuredMedia = $record->getFirstMedia('featured');
                    if ($featuredMedia) {
                        $component->state([
                            'existing_url' => $featuredMedia->getUrl(),
                            'existing_id' => $featuredMedia->id,
                            'caption' => $featuredMedia->getCustomProperty('caption', ''),
                            'alt_text' => $featuredMedia->getCustomProperty('alt_text', ''),
                        ]);
                    }
                }
            })
            ->dehydrated(false); // We'll handle saving manually
    }
}