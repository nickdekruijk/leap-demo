<?php

namespace Tests\Feature;

use App\Models\News;
use Database\Seeders\NewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The date on a news item. An event has a date column and always showed one; news has
 * only published_at, so its cards and detail page showed no date at all. Being
 * published is what dates a news item, so that is the date it shows — through a `date`
 * accessor, which is what the shared item views ask every content type for.
 */
class ItemDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_news_items_date_is_its_publication_moment(): void
    {
        $news = $this->seedNews();

        $this->assertNotNull($news->date);
        $this->assertTrue($news->date->equalTo($news->published_at));
    }

    public function test_the_detail_page_shows_the_publication_date(): void
    {
        $news = $this->seedNews();

        $html = $this->get($this->itemPath('News', $news->slug))->assertOk()->getContent();

        $this->assertStringContainsString('<dt>'.__('Date').'</dt>', $html);
        $this->assertStringContainsString('<time datetime="'.$news->published_at->toDateString().'">', $html);
    }

    /**
     * The weekday leads the date. A detail page has the room to write it out; Dutch
     * lowercases day names, but here it opens the line and is capitalised.
     */
    public function test_the_detail_page_writes_the_weekday_out_in_full(): void
    {
        $news = $this->seedNews();

        $html = $this->get($this->itemPath('News', $news->slug))->assertOk()->getContent();

        $expected = Str::ucfirst($news->published_at->translatedFormat('l j F Y'));

        $this->assertStringContainsString('>'.$expected.'</time>', $html);
    }

    /**
     * A card has less room, so it abbreviates — and without the full stop Dutch would
     * normally put after it ("do."), which would read as a typo next to the number.
     */
    public function test_the_card_abbreviates_the_weekday_without_a_full_stop(): void
    {
        $news = $this->seedNews();

        $html = $this->get($this->overviewPath('News'))->assertOk()->getContent();

        $weekday = Str::ucfirst(rtrim($news->published_at->translatedFormat('D'), '.'));
        $expected = $weekday.' '.$news->published_at->translatedFormat('j F Y');

        $this->assertStringContainsString('>'.$expected.'</time>', $html);
        $this->assertStringNotContainsString('>'.$weekday.'. ', $html);
    }

    public function test_the_card_shows_the_publication_date(): void
    {
        $news = $this->seedNews();

        $html = $this->get($this->overviewPath('News'))->assertOk()->getContent();

        $this->assertStringContainsString('class="item-date"', $html);
        $this->assertStringContainsString('<time datetime="'.$news->published_at->toDateString().'">', $html);
    }

    /**
     * News is listed newest first, so the first item is the one whose date the overview
     * shows at the top — the same item the detail-page assertions above visit.
     */
    private function seedNews(): News
    {
        if (! class_exists(News::class)) {
            $this->markTestSkipped('Installed without the news content type.');
        }

        $this->seed(NewsSeeder::class);

        return News::active()->firstOrFail();
    }
}
