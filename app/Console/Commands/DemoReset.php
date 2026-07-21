<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

#[Signature('demo:reset {--now : Reset immediately, ignoring the debounce timer}')]
#[Description('Reset the demo site to its seeded state once the demo user has been idle long enough')]
class DemoReset extends Command
{
    /**
     * Runs every minute on the demo host. Each change by the demo user moves
     * the last-change marker, so the reset only fires once the site has been
     * untouched for demo.reset_after minutes (a debounce, not a fixed cycle).
     */
    public function handle(): int
    {
        if (! config('demo.enabled')) {
            $this->warn('Demo mode is disabled; refusing to reset this installation.');

            return self::FAILURE;
        }

        $cache = Cache::store(config('demo.cache_store'));
        $lastChange = $cache->get(config('demo.cache_key'));

        if (! $this->option('now')) {
            if (! $lastChange) {
                $this->info('Nothing to reset.');

                return self::SUCCESS;
            }

            if (Carbon::parse($lastChange)->gt(now()->subMinutes(config('demo.reset_after')))) {
                $this->info('Last change is too recent, not resetting yet.');

                return self::SUCCESS;
            }
        }

        // Forget the marker before resetting so a change made while the reset
        // runs arms a fresh timer instead of being lost
        $cache->forget(config('demo.cache_key'));

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->cleanStorage();
        $this->call('cache:clear');

        $this->info('Demo site reset to seeded state.');

        return self::SUCCESS;
    }

    /**
     * Remove everything the demo user uploaded to the public disk, keeping
     * the seeded baseline files and .gitignore placeholders.
     */
    protected function cleanStorage(): void
    {
        $disk = Storage::disk('public');
        $keep = config('demo.baseline_files');

        foreach ($disk->allFiles() as $file) {
            if (in_array($file, $keep) || basename($file) === '.gitignore') {
                continue;
            }
            $disk->delete($file);
        }

        $directories = collect($disk->allDirectories())
            ->sortByDesc(fn (string $directory): int => substr_count($directory, '/'));

        foreach ($directories as $directory) {
            if ($disk->allFiles($directory) === []) {
                $disk->deleteDirectory($directory);
            }
        }
    }
}
