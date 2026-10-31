<?php

return [
    // Preferred 30-minute test-ride slots: 10:00 through 19:30.
    'ride_slots' => array_map(
        fn (int $i): string => sprintf(
            '%02d:%02d',
            intdiv($i, 2),
            ($i % 2) * 30
        ),
        range(20, 39)
    ),

    // Public hero image.
    'hero_image' => 'images/hero/hero-image.webp',

    // Social media URLs.
    'instagram' => env('SAQR_INSTAGRAM_URL') ?: 'https://www.instagram.com/',
    'facebook' => env('SAQR_FACEBOOK_URL') ?: 'https://www.facebook.com/',
    'youtube' => env('SAQR_YOUTUBE_URL') ?: 'https://www.youtube.com/',
    'tiktok' => env('SAQR_TIKTOK_URL') ?: 'https://www.tiktok.com/',

    // Assessment demo accounts.
    'admin_email' => env('SAQR_ADMIN_EMAIL'),
    'admin_password' => env('SAQR_ADMIN_PASSWORD'),

    'data_entry_email' => env('SAQR_DATA_ENTRY_EMAIL'),
    'data_entry_password' => env('SAQR_DATA_ENTRY_PASSWORD'),

    // Explicitly allow demo seeding outside local/testing.
    // Keep disabled except during initial assessment setup.
    'allow_demo_seeding' => env(
        'SAQR_ALLOW_DEMO_SEEDING',
        false
    ),
];
