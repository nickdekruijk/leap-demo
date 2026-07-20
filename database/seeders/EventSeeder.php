<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Page;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureOverviewPage();

        if (Event::count() > 0) {
            return;
        }

        // Dated on purpose rather than at random. An overview shows upcoming events by
        // default, so a date spanning today would make a seeded set turn up at a
        // different size on every reseed — and the two deliberate cases below could land
        // in the past, where the thing they exist to demonstrate is never seen.
        //
        // Six upcoming and six finished, so the overview always looks the same and a
        // section set to past (or both) has as much to show as the default one.
        Event::factory()->future()->count(4)->create()
            ->push(Event::factory()->future()->pastMidnight()->create())
            ->push(Event::factory()->future()->withoutEndTime()->create())
            ->concat(Event::factory()->past()->count(6)->create())
            ->each(function (Event $item): void {
                $item->tags()->sync(Tag::inRandomOrder()->limit(rand(1, 2))->pluck('id'));
            });
    }

    /**
     * The overview page this type's cards hang under, created once. The unlimited,
     * untagged "events" section is what makes it the canonical overview (see
     * PageController::overviewPage()) and gives every item its detail URL.
     *
     * Title and slug are per locale: give "Events" a translation in a lang file
     * and that locale gets that name, with the slug derived from it. A locale without
     * one falls back to the English word rather than to nothing.
     */
    protected function ensureOverviewPage(): void
    {
        $exists = Page::all()->contains(
            fn (Page $page): bool => collect($page->sections ?? [])->contains(
                fn ($section): bool => ($section['_name'] ?? null) === 'events' && empty($section['limit']) && empty($section['tag'])
            )
        );

        if ($exists) {
            return;
        }

        $locales = array_keys(config('leap.locales') ?: [app()->getLocale() => null]);
        $title = collect($locales)->mapWithKeys(fn ($locale): array => [$locale => __('Events', [], $locale)])->all();
        $slug = collect($title)->map(fn (string $value): string => Str::slug($value))->all();

        Page::create([
            'active' => true,
            'menuitem' => true,
            'title' => $title,
            'slug' => $slug,
            'sort' => 100 + (int) array_search('events', array_keys(config('leap.content') ?: [])), // config order → menu/section order
            'sections' => [[
                '_name' => 'events',
                '_view' => 'sections.items',
                '_sort' => 0,
                'active' => true,
                'head' => $title,
            ]],
        ]);
    }
}
