<?php

return [
    'listing_packages' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 0,
            'listing_credits' => 1,
            'featured_credits' => 0,
            'max_active_listing_credits' => 1,
            'sort_order' => 1,
        ],
        'featured' => [
            'name' => 'Featured',
            'price' => 15000,
            'listing_credits' => 8,
            'featured_credits' => 0,
            'sort_order' => 2,
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 25000,
            'listing_credits' => 15,
            'featured_credits' => 0,
            'sort_order' => 3,
        ],
        'spotlight' => [
            'name' => 'Spotlight',
            'price' => 25000,
            'listing_credits' => 15,
            'featured_credits' => 5,
            'sort_order' => 4,
        ],
    ],
    'listing_addons' => [
        'featured_addon_1' => [
            'name' => 'Featured Add-on (1)',
            'price' => 3000,
            'listing_credits' => 0,
            'featured_credits' => 1,
            'featured_expires_days' => 30,
            'sort_order' => 1,
        ],
        'featured_addon_3' => [
            'name' => 'Featured Add-on (3)',
            'price' => 5000,
            'listing_credits' => 0,
            'featured_credits' => 3,
            'featured_expires_days' => 30,
            'sort_order' => 2,
        ],
    ],
];
