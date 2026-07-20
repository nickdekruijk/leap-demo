<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\News;
use App\Models\Tag;
use Database\Seeders\EventSeeder;
use Database\Seeders\NewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ResolvesContentPaths;
use Tests\TestCase;

/**
 * The tags on a detail page. They used to be a row in the meta definition list — a
 * "Tags" label beside the names glued together with commas — while the overview above
 * showed those same tags as chips. They are chips in both places now.
 */
class ItemTagsTest extends TestCase
{
    use RefreshDatabase;
    use ResolvesContentPaths;

    public function test_an_items_tags_render_as_a_chip_list(): void
    {
        $news = $this->seedTaggedNews();

        $html = $this->get($this->itemPath('News', $news->slug))->assertOk()->getContent();

        $this->assertStringContainsString('<ul class="item-tags"', $html);

        foreach ($news->tags as $tag) {
            $this->assertStringContainsString('<li>'.$tag->name.'</li>', $html);
        }
    }

    public function test_the_tags_are_no_longer_a_row_in_the_meta_list(): void
    {
        $news = $this->seedTaggedNews();

        $html = $this->get($this->itemPath('News', $news->slug))->assertOk()->getContent();

        $this->assertStringNotContainsString('<dt>'.__('Tags').'</dt>', $html);

        // Two or more names run together with a comma is the old dd. A single tag is
        // just its own name, which the chip markup contains as well, so there would be
        // nothing to tell apart.
        if ($news->tags->count() > 1) {
            $this->assertStringNotContainsString($news->tags->pluck('name')->join(', '), $html);
        }
    }

    /**
     * An event does have a date, so it still has a meta list — taking the tags out of
     * that list must not have taken the rest of it with them.
     */
    public function test_an_item_with_a_date_still_renders_its_meta_list(): void
    {
        $event = $this->seedTaggedEvents();

        $html = $this->get($this->itemPath('Events', $event->slug))->assertOk()->getContent();

        $this->assertStringContainsString('<dl class="item-meta">', $html);
        $this->assertStringContainsString('<dt>'.__('Date').'</dt>', $html);
        $this->assertStringContainsString('<ul class="item-tags"', $html);
    }

    /**
     * A seeded news item with tags. The seeders hand out whichever tags already exist,
     * so a fresh test database needs them made first — two of them, so the comma-joined
     * form of the old markup is something the assertions can look for.
     */
    private function seedTaggedNews(): News
    {
        if (! class_exists(News::class)) {
            $this->markTestSkipped('Installed without the news content type.');
        }

        Tag::factory()->count(2)->create();
        $this->seed(NewsSeeder::class);

        $news = News::query()->whereHas('tags')->first();

        $this->assertNotNull($news, 'Expected the seeder to tag at least one news item');

        return $news;
    }

    private function seedTaggedEvents(): Event
    {
        if (! class_exists(Event::class)) {
            $this->markTestSkipped('Installed without the events content type.');
        }

        Tag::factory()->count(2)->create();
        $this->seed(EventSeeder::class);

        $event = Event::query()->whereHas('tags')->first();

        $this->assertNotNull($event, 'Expected the seeder to tag at least one event');

        return $event;
    }
}
