<?php

if (!function_exists('formatNaira')) {
    /**
     * Format an amount into Nigerian Naira format.
     *
     * @param  float  $amount
     * @return string
     */
    function formatNaira($amount)
    {
        $amount = floatval($amount);
        // Check if the amount has a fractional part that's not .00
        $fractionalPart = $amount - floor($amount);
        if ($fractionalPart > 0) {
            return '₦' . number_format($amount, 2, '.', ',');
        }

        // If it doesn't have a fractional part or is .00, just show the whole number
        return '₦' . number_format($amount);
    }
}

if (!function_exists('getPropertyImageConfig')) {
    /**
     * Get image configuration based on property type.
     *
     * @param  string|null  $propertyTypeSlug
     * @return array
     */
    function getPropertyImageConfig($propertyTypeSlug = null)
    {
        $configs = [
            'apartment' => [
                'gallery_max_files' => 10,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 10 high-quality images showcasing rooms, amenities, and building features.',
            ],
            'house' => [
                'gallery_max_files' => 10,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 10 high-quality images showcasing interior, exterior, and compound features.',
            ],
            'land' => [
                'gallery_max_files' => 4,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 4 high-quality images showing the land, access roads, and surroundings.',
            ],
            'commercial' => [
                'gallery_max_files' => 5,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 5 high-quality images showcasing the commercial space, storefront, and facilities.',
            ],
            'office-space' => [
                'gallery_max_files' => 8,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 8 high-quality images showcasing office layout, amenities, and building features.',
            ],
            'warehouse' => [
                'gallery_max_files' => 6,
                'featured_required' => true,
                'gallery_helper_text' => 'Upload up to 6 high-quality images showcasing warehouse space, loading areas, and facilities.',
            ],
        ];

        $defaultConfig = [
            'gallery_max_files' => 10,
            'featured_required' => true,
            'gallery_helper_text' => 'Upload high-quality images showcasing the property.',
        ];

        return $configs[$propertyTypeSlug] ?? $defaultConfig;
    }
}

if (!function_exists('getOptimalImageResolution')) {
    /**
     * Get optimal image resolution specifications.
     *
     * @return array
     */
    function getOptimalImageResolution()
    {
        return [
            'min_width' => 1024,
            'min_height' => 683,
            'recommended_width' => 1440,
            'recommended_height' => 960,
            'aspect_ratio' => '3:2',
            'max_file_size' => 5120, // 5MB in KB
            'formats' => ['image/jpeg', 'image/png', 'image/webp'],
            'quality_note' => 'For best results, use images that are at least 1440×960px in 3:2 aspect ratio (landscape orientation).',
        ];
    }
}
