<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

#[Signature('demo:images {--only= : Only paths starting with this, e.g. news/ or pages/contact.jpg} {--force : Regenerate images that already exist} {--dry-run : List what would be generated without calling the API}')]
#[Description('Generate the demo site\'s photographs from database/seeders/demo-images.php')]
class DemoImages extends Command
{
    /**
     * The sizes OpenAI accepts. Checked before the first call, so a typo in the
     * manifest costs nothing instead of failing thirty times over.
     */
    protected const SIZES = ['1024x1024', '1536x1024', '1024x1536', 'auto'];

    /**
     * Run once and commit the result: the demo host is served the images from the
     * repository and never needs an API key of its own. Regenerating a single
     * photograph that came out wrong is what --only and --force are for.
     */
    public function handle(): int
    {
        $manifest = require database_path('seeders/demo-images.php');
        $target = database_path('seeders/demo-images');

        $images = $this->selected($manifest);

        if ($images === []) {
            $this->warn('Nothing to generate.');

            return self::SUCCESS;
        }

        if ($invalid = $this->invalidSizes($manifest, $images)) {
            $this->error('Unsupported size in the manifest: '.implode(', ', $invalid));
            $this->line('Allowed: '.implode(', ', self::SIZES));

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            foreach ($images as $path => $image) {
                $this->line(($this->exists($target, $path) ? '  skip  ' : '  make  ').$path);
            }
            $this->info(count($images).' image(s) in scope.');

            return self::SUCCESS;
        }

        $key = config('leap.ai.providers.openai.api_key');

        if (! $key) {
            $this->error('No OPENAI_API_KEY set — see leap.ai.providers.openai.');

            return self::FAILURE;
        }

        $tokens = 0;

        foreach ($images as $path => $image) {
            if ($this->exists($target, $path) && ! $this->option('force')) {
                $this->line('  skip  '.$path);

                continue;
            }

            $this->getOutput()->write('  make  '.$path.' … ');

            $response = Http::withToken($key)
                ->timeout(config('leap.ai.timeout', 60) * 5)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => config('demo.image_model'),
                    'prompt' => $image['prompt'].' '.$manifest['style'],
                    'size' => $image['size'] ?? $manifest['size'],
                    'quality' => config('demo.image_quality'),
                    'output_format' => 'jpeg',
                    'output_compression' => 80,
                    'n' => 1,
                ]);

            if (! $response->successful()) {
                $this->newLine();
                // The provider's own wording says more than this command could add:
                // a depleted balance, an unverified organisation, a refused prompt
                $this->error($response->json('error.message') ?: $response->body());

                return self::FAILURE;
            }

            $bytes = base64_decode($response->json('data.0.b64_json'));
            File::ensureDirectoryExists(dirname($target.'/'.$path));
            File::put($target.'/'.$path, $bytes);

            $tokens += $response->json('usage.output_tokens') ?? 0;
            $this->line(round(strlen($bytes) / 1024).' KB');
        }

        $this->info('Done. '.number_format($tokens).' output tokens billed.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @return array<string, array<string, string>>
     */
    protected function selected(array $manifest): array
    {
        $only = $this->option('only');

        return collect($manifest['images'])
            ->filter(fn (array $image, string $path): bool => ! $only || str_starts_with($path, $only))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, array<string, string>>  $images
     * @return array<int, string>
     */
    protected function invalidSizes(array $manifest, array $images): array
    {
        return collect($images)
            ->map(fn (array $image): string => $image['size'] ?? $manifest['size'])
            ->reject(fn (string $size): bool => in_array($size, self::SIZES))
            ->unique()
            ->values()
            ->all();
    }

    protected function exists(string $target, string $path): bool
    {
        return File::exists($target.'/'.$path);
    }
}
