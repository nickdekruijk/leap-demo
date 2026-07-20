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
        // default, so a random date spanning today meant a seeded set of six turned up
        // as anywhere between two and six — and the two deliberate cases below could
        // land in the past and never be seen at all.
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
     * The overview page this type's cards hang under, created once. The
     * unlimited, untagged "events" section is what makes it the canonical overview
     * (see PageController::overviewPage()) and gives every item its detail URL.
     *
     * Title and slug are per locale: a Dutch site gets Agenda at /agenda, an English
     * one Events at /events. Add the title to a lang file to name it in a new locale —
     * an untranslated locale falls back to the English word rather than to nothing.
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
