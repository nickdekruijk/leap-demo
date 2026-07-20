<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Page;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureOverviewPage();

        if (News::count() > 0) {
            return;
        }

        News::factory()->count(6)->create()->each(function (News $item): void {
            $item->tags()->sync(Tag::inRandomOrder()->limit(rand(1, 2))->pluck('id'));
        });
    }

    /**
     * The overview page this type's cards hang under (/news), created once. The
     * unlimited, untagged "news" section is what makes it the canonical overview
     * (see PageController::overviewPage()) and gives every item its detail URL. Rename
     * its slug per locale in the admin (e.g. nieuws / news).
     */
    protected function ensureOverviewPage(): void
    {
        $exists = Page::all()->contains(
            fn (Page $page): bool => collect($page->sections ?? [])->contains(
                fn ($section): bool => ($section['_name'] ?? null) === 'news' && empty($section['limit']) && empty($section['tag'])
            )
        );

        if ($exists) {
            return;
        }

        $locales = array_keys(config('leap.locales') ?: [app()->getLocale() => null]);
        $translate = fn (string $value): array => collect($locales)->mapWithKeys(fn ($locale): array => [$locale => $value])->all();

        Page::create([
            'active' => true,
            'menuitem' => true,
            'title' => $translate('News'),
            'slug' => $translate('news'),
            'sort' => 100 + (int) array_search('news', array_keys(config('leap.content') ?: [])), // config order → menu/section order
            'sections' => [[
                '_name' => 'news',
                '_view' => 'sections.items',
                '_sort' => 0,
                'active' => true,
                'head' => $translate('News'),
            ]],
        ]);
    }
}
