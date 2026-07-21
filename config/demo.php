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

];
