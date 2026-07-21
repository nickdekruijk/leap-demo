<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use NickDeKruijk\Leap\Models\Log;

class RecordDemoChange
{
    /**
     * Record the moment of any Eloquent write made by the demo user, so the
     * scheduled demo:reset knows when the site was last changed. Leap logs
     * every action (including logging in) to its Log model, so those writes
     * are ignored: merely signing in must not schedule a reset.
     *
     * @param  array<int, mixed>  $payload
     */
    public function handle(string $event, array $payload): void
    {
        if (! config('demo.enabled')) {
            return;
        }

        if ($payload[0] instanceof Log) {
            return;
        }

        if (Auth::guard(config('leap.guard'))->user()?->email !== config('demo.email')) {
            return;
        }

        Cache::store(config('demo.cache_store'))->forever(config('demo.cache_key'), now()->toIso8601String());
    }
}
