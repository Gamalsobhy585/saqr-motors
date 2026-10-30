<?php

return [
    // Preferred 30-minute test-ride slots (store branches' actual hours separately if required).
    'ride_slots' => array_map(
        fn (int $i): string => sprintf('%02d:%02d', intdiv($i, 2), ($i % 2) * 30),
        range(20, 39) // 10:00 through 19:30
    ),
    // A dedicated, brand-independent hero asset supplied by the designer.
    'hero_image' => 'images/hero/hero-image.webp',

    // General platform URLs are assessment placeholders, not SAQR brand accounts.
    'instagram' => env('SAQR_INSTAGRAM_URL') ?: 'https://www.instagram.com/',
    'facebook' => env('SAQR_FACEBOOK_URL') ?: 'https://www.facebook.com/',
    'youtube' => env('SAQR_YOUTUBE_URL') ?: 'https://www.youtube.com/',
    'tiktok' => env('SAQR_TIKTOK_URL') ?: 'https://www.tiktok.com/',
];
