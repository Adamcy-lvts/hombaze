@php
    $state = $getState();
    $existingImageUrl = $state['existing_url'] ?? null;
    $existingImageId = $state['existing_id'] ?? null;
    $caption = $state['caption'] ?? '';
    $altText = $state['alt_text'] ?? '';
    $statePath = $getStatePath();
    $componentId = 'featured-image-upload-' . str_replace(['[', ']', '.'], '-', $statePath);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="w-full">
        @livewire('featured-image-upload', [
            'existingImageUrl' => $existingImageUrl,
            'existingImageId' => $existingImageId,
            'caption' => $caption,
            'altText' => $altText,
            'required' => true
        ], key($componentId . '-' . now()->timestamp))
    </div>
</x-dynamic-component>