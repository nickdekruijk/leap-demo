<?php

namespace Tests\Feature;

use App\Http\Controllers\PageController;
use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use NickDeKruijk\Leap\Models\Mediable;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\ResolvesContentPaths;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;
    use ResolvesContentPaths;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoSeeder::class);
    }

    /**
     * @return array<string, array{0: class-string<Model>}>
     */
    public static function contentTypes(): array
    {
        return [
            'news' => [News::class],
            'events' => [Event::class],
            'projects' => [Project::class],
        ];
    }

    /**
     * What the whole seeder exists for: text a visitor can read, in both languages,
     * instead of the same faker sentence repeated under every locale.
     *
     * The intro is what proves the translation: a title can legitimately read the
     * same in both languages ("Repair café"), a sentence of prose cannot.
     *
     * @param  class-string<Model>  $model
     */
    #[DataProvider('contentTypes')]
    public function test_every_item_is_written_in_dutch_and_english(string $model): void
    {
        foreach ($model::all() as $item) {
            $title = $item->getTranslations('title');
            $intro = $item->getTranslations('intro');

            $this->assertNotEmpty($title['nl']);
            $this->assertNotEmpty($title['en']);
            $this->assertNotEmpty($intro['nl']);
            $this->assertNotSame($intro['nl'], $intro['en'], 'Both locales carry the same text, so nothing was translated.');
        }
    }

    public function test_every_page_is_written_in_dutch_and_english(): void
    {
        foreach (Page::all() as $page) {
            $title = $page->getTranslations('title');

            $this->assertNotEmpty($title['nl']);
            $this->assertNotEmpty($title['en']);
        }
    }

    public function test_it_seeds_the_whole_site(): void
    {
        $this->assertSame(6, News::count());
        $this->assertSame(12, Event::count());
        $this->assertSame(6, Project::count());

        // Six pages of its own plus an overview per content type
        $this->assertSame(9, Page::count());
    }

    /**
     * @param  class-string<Model>  $model
     */
    #[DataProvider('contentTypes')]
    public function test_every_item_has_one_text_section_ready_for_an_image(string $model): void
    {
        foreach ($model::all() as $item) {
            $this->assertCount(1, $item->sections);

            $section = $item->sections[0];

            $this->assertSame('default', $section['_name']);
            $this->assertSame('right', $section['image_position']);
            $this->assertTrue($section['active']);
            $this->assertNotEmpty($section['head']['nl']);
            $this->assertNotEmpty($section['head']['en']);
            $this->assertStringStartsWith('<p>', $section['body']['nl']);
            $this->assertStringStartsWith('<p>', $section['body']['en']);
        }
    }

    /**
     * @param  class-string<Model>  $model
     */
    #[DataProvider('contentTypes')]
    public function test_every_item_carries_at_least_one_tag(string $model): void
    {
        foreach ($model::all() as $item) {
            $this->assertNotEmpty($item->tags, 'An item without tags leaves a gap in the filter chips.');
        }
    }

    public function test_each_content_type_has_an_overview_its_items_hang_under(): void
    {
        foreach (['events', 'news', 'projects'] as $type) {
            $this->assertNotNull(PageController::overviewPage($type), "No overview page for {$type}.");
        }
    }

    public function test_an_item_detail_page_resolves(): void
    {
        $news = News::first();

        $this->get($this->itemPath('News', $news->slug))
            ->assertOk()
            ->assertSee($news->title);
    }

    public function test_the_home_page_teases_every_content_type(): void
    {
        $sections = collect(Page::find(1)->sections);

        foreach (['events', 'news', 'projects'] as $type) {
            $teaser = $sections->firstWhere('_name', $type);

            $this->assertNotNull($teaser, "The home page has no {$type} teaser.");
            $this->assertSame(6, $teaser['limit']);
        }
    }

    /**
     * The same cards listed more than one way, so the demo shows what the section's
     * layout and column fields do rather than repeating the default three times.
     */
    public function test_the_listings_do_not_all_look_the_same(): void
    {
        $home = collect(Page::find(1)->sections);

        $this->assertSame('grid', $home->firstWhere('_name', 'projects')['layout']);
        $this->assertSame(4, $home->firstWhere('_name', 'news')['columns']);
        $this->assertSame(4, PageController::overviewPage('news')->sections[0]['columns']);
    }

    /**
     * The overview shows upcoming events by default, so both halves have to be
     * filled: an empty past means a section set to "past" has nothing to show.
     */
    public function test_half_of_the_events_are_still_to_come(): void
    {
        $this->assertSame(6, Event::future()->count());
        $this->assertSame(6, Event::past()->count());
    }

    /**
     * The two cases Event has rules for, deliberately in the set rather than left to
     * chance: an event that never says when it stops, and one that ends after midnight.
     */
    public function test_the_events_include_the_cases_the_model_handles(): void
    {
        $this->assertSame(1, Event::whereNull('end_time')->count());

        $overnight = Event::whereColumn('end_time', '<=', 'start_time')->whereNotNull('end_time')->first();

        $this->assertNotNull($overnight, 'No event runs past midnight.');
        $this->assertTrue(
            $overnight->ends_at->isAfter($overnight->date->endOfDay()),
            'An event ending after midnight finishes the next day.',
        );
    }

    public function test_news_is_dated_backwards_from_today(): void
    {
        $dates = News::orderByDesc('published_at')->pluck('published_at');

        $this->assertTrue($dates->first()->isPast());
        $this->assertTrue($dates->last()->isBefore($dates->first()));
    }

    public function test_projects_are_ordered_by_hand(): void
    {
        $this->assertSame([1, 2, 3, 4, 5, 6], Project::orderBy('sort')->pluck('sort')->all());
    }

    /**
     * The manifest and the copy have to agree even on a clone that has never run
     * demo:images: an item pointing at a photograph nobody generates would fail
     * silently, showing a placeholder gradient and nothing else.
     */
    public function test_every_item_points_at_a_photograph_the_manifest_describes(): void
    {
        $manifest = require database_path('seeders/demo-images.php');
        $content = require database_path('seeders/demo-content.php');

        $used = collect([$content['news'], $content['events']['future'], $content['events']['past'], $content['projects']])
            ->flatten(1)
            ->pluck('image');

        $this->assertCount(24, $used->filter(), 'Every content item names an image.');
        $this->assertSame($used->unique()->count(), $used->count(), 'No two items share a photograph.');

        foreach ($used as $file) {
            $this->assertArrayHasKey($file, $manifest['images'], "{$file} is used but not in the manifest.");
        }

        $items = collect($manifest['images'])->keys()->reject(fn (string $path): bool => str_starts_with($path, 'pages/'));

        $this->assertEqualsCanonicalizing($used->all(), $items->all(), 'The manifest describes exactly the item photographs in use.');
    }

    /**
     * @param  class-string<Model>  $model
     */
    #[DataProvider('contentTypes')]
    public function test_every_item_carries_its_photograph(string $model): void
    {
        $this->skipWithoutImages();

        foreach ($model::all() as $item) {
            $media = $item->media;

            $this->assertCount(1, $media, 'An item has exactly one photograph.');
            $this->assertSame('images', $media->first()->pivot->mediable_attribute);
            Storage::disk('public')->assertExists($media->first()->file_name);
            $this->assertNotEmpty($media->first()->alt('nl'));
            $this->assertNotEmpty($media->first()->alt('en'));
        }
    }

    /**
     * A section's photograph is not in the sections JSON but in a pivot row naming
     * the section's own array key, which is the part that is easy to get wrong.
     */
    public function test_the_pages_carry_their_section_photographs(): void
    {
        $this->skipWithoutImages();

        $home = Mediable::where('mediable_type', Page::class)->where('mediable_id', 1)->pluck('mediable_attribute');

        $this->assertEqualsCanonicalizing([
            'sections.0.image', 'sections.1.image', 'sections.2.image',
            'sections.3.image', 'sections.4.image', 'sections.5.image',
        ], $home->all());

        foreach ([2, 3, 4] as $id) {
            $this->assertSame(['sections.0.image'], Mediable::where('mediable_type', Page::class)->where('mediable_id', $id)->pluck('mediable_attribute')->all());
        }

        // The legal pages are deliberately without
        $this->assertSame(0, Mediable::where('mediable_type', Page::class)->whereIn('mediable_id', [5, 6])->count());
    }

    protected function skipWithoutImages(): void
    {
        if (! File::exists(database_path('seeders/demo-images/news'))) {
            $this->markTestSkipped('No photographs generated yet — run demo:images.');
        }
    }

    public function test_it_creates_the_demo_account_as_a_superuser(): void
    {
        $user = User::firstWhere('email', config('demo.email'));

        $this->assertNotNull($user);
        $this->assertDatabaseHas('leap_role_user', ['user_id' => $user->id, 'role_id' => 1]);
    }

    public function test_seeding_twice_changes_nothing(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(1, User::where('email', config('demo.email'))->count());
        $this->assertSame(1, DB::table('leap_role_user')->count());
        $this->assertSame(6, News::count());
        $this->assertSame(12, Event::count());
        $this->assertSame(6, Project::count());
        $this->assertSame(9, Page::count());
    }
}
