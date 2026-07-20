<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureOverviewPage();

        if (Project::count() > 0) {
            return;
        }

        Project::factory()->count(6)->create()->each(function (Project $item): void {
            $item->tags()->sync(Tag::inRandomOrder()->limit(rand(1, 2))->pluck('id'));
        });
    }

    /**
     * The overview page this type's cards hang under (/projects), created once. The
     * unlimited, untagged "projects" section is what makes it the canonical overview
     * (see PageController::overviewPage()) and gives every item its detail URL. Rename
     * its slug per locale in the admin (e.g. nieuws / news).
     */
    protected function ensureOverviewPage(): void
    {
        $exists = Page::all()->contains(
            fn (Page $page): bool => collect($page->sections ?? [])->contains(
                fn ($section): bool => ($section['_name'] ?? null) === 'projects' && empty($section['limit']) && empty($section['tag'])
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
            'title' => $translate('Projects'),
            'slug' => $translate('projects'),
            'sort' => 100 + (int) array_search('projects', array_keys(config('leap.content') ?: [])), // config order → menu/section order
            'sections' => [[
                '_name' => 'projects',
                '_view' => 'sections.items',
                '_sort' => 0,
                'active' => true,
                'head' => $translate('Projects'),
            ]],
        ]);
    }
}
