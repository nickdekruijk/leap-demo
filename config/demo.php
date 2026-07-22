<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | When enabled the application acts as a public demo: every change made by
    | the demo user is recorded and the whole site (database and uploads) is
    | reset to its seeded state once no changes have been made for the amount
    | of minutes configured in reset_after.
    |
    */

    'enabled' => env('DEMO_MODE', false),

    // The account demo visitors log in with; created by DemoUserSeeder
    'email' => env('DEMO_EMAIL', 'info@example.com'),

    // Publicly documented password for the demo account
    'password' => env('DEMO_PASSWORD', 'leapdemo'),

    // Minutes after the last demo change before the site is reset
    'reset_after' => env('DEMO_RESET_AFTER', 15),

    // Cache store for the last-change marker; must survive migrate:fresh,
    // so never point this at the database store
    'cache_store' => env('DEMO_CACHE_STORE', 'file'),

    'cache_key' => 'demo:last_change_at',

    // Files on the public disk that the reset must keep
    'baseline_files' => [
        'video-posters/youtube-dQw4w9WgXcQ.jpg',
    ],

    // The OpenAI model demo:images generates the seeded photographs with. Its
    // key comes from leap.ai.providers.openai, so no second credential.
    'image_model' => env('DEMO_IMAGE_MODEL', 'gpt-image-2'),

    // Image quality, and with it the price: left on 'auto' the model decides for
    // itself and a detailed prompt lands on the expensive end. The site never
    // shows these wider than 1600px, so a fixed middle setting is enough.
    'image_quality' => env('DEMO_IMAGE_QUALITY', 'medium'),

];
