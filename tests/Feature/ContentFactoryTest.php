<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\News;
use Database\Seeders\NewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Seeded content items are what a fresh install is judged on, and an item without a
 * section renders a detail page that is only a header — the very thing the sections
 * exist to fill. The factory seeds one "text with image" section so there is something
 * to look at, and to edit in the admin.
 */
class ContentFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{class-string}>
     */
    public static function contentModels(): array
    {
        return [
            'news' => [News::class],
            'events' => [Event::class],
        ];
    }

    #[DataProvider('contentModels')]
    public function test_a_seeded_item_gets_a_text_section_with_the_image_on_the_right(string $model): void
    {
        $item = $model::factory()->create();

        $this->assertCount(1, $item->sections);

        $section = $item->sections[0];

        $this->assertSame('default', $section['_name']);
        $this->assertSame('right', $section['image_position']);
        $this->assertTrue($section['active']);
    }

    #[DataProvider('contentModels')]
    public function test_the_seeded_section_carries_head_and_body_for_every_locale(string $model): void
    {
        config(['leap.locales' => ['nl' => 'Nederlands', 'en' => 'English']]);

        $section = $model::factory()->create()->sections[0];

        foreach (['head', 'body'] as $field) {
            $this->assertSame(['nl', 'en'], array_keys($section[$field]));
            $this->assertNotEmpty($section[$field]['nl']);
        }

        // richtext, so the body is HTML rather than the bare sentence the intro is.
        $this->assertStringStartsWith('<p>', $section['body']['nl']);
    }

    /**
     * A page's first section carries the h1; an item's header already has one, so the
     * same section must step down to h2 there rather than compete with it.
     */
    public function test_an_items_first_section_heading_is_an_h2_below_the_item_title(): void
    {
        // Via the seeder, because an item's detail URL only resolves under its overview
        // page — which is what the seeder creates.
        $this->seed(NewsSeeder::class);
        $news = News::first();

        $response = $this->get($this->itemPath('News', $news->slug));

        $response->assertOk();

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '<h1'), 'An item page has exactly one h1: its title.');
        $this->assertStringContainsString('<h1>'.$news->title.'</h1>', $html);
        $this->assertStringContainsString('<h2>'.$news->sections[0]['head'][app()->getLocale()].'</h2>', $html);
    }
}
