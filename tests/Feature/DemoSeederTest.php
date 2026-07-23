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
use Illuminate\Support\Str;
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
     * What the hand-written content exists for: text a visitor can read, in both
     * languages, instead of the same faker sentence repeated under every locale.
     *
     * The intro is what proves the translation: a title can legitimately read the
     * same in both languages, a sentence of prose cannot.
     *
     * News is exempt — those items are generated from the package's changelog, which
     * is written in English, and release notes are not translated.
     *
     * @param  class-string<Model>  $model
     */
    #[DataProvider('contentTypes')]
    public function test_every_item_is_written_in_dutch_and_english(string $model): void
    {
        if ($model === News::class) {
            $this->markTestSkipped('Release notes come from the English changelog.');
        }

        foreach ($model::all() as $item) {
            $title = $item->getTranslations('title');
            $intro = $item->getTranslations('intro');

            $this->assertNotEmpty($title['nl']);
            $this->assertNotEmpty($title['en']);
            $this->assertNotEmpty($intro['nl']);
            $this->assertNotSame($intro['nl'], $intro['en'], 'Both locales carry the same text, so nothing was translated.');
        }
    }

    /**
     * Every page but the one that exists to show what an untranslated page does.
     */
    public function test_every_page_is_written_in_dutch_and_english(): void
    {
        foreach (Page::all()->except(6) as $page) {
            $title = $page->getTranslations('title');

            $this->assertNotEmpty($title['nl'] ?? null, 'Page '.$page->id.' has no Dutch title.');
            $this->assertNotEmpty($title['en']);
        }
    }

    /**
     * A slug is an address, not prose: without one in a locale the page is not served
     * there at all, rather than answering on another language's URL.
     */
    public function test_the_untranslated_page_has_no_dutch_address(): void
    {
        $page = Page::find(6);

        $this->assertSame(['en' => 'untranslated-example'], array_filter($page->getTranslations('slug')));
        $this->get('/features/multilingual/untranslated-example')->assertOk();
        $this->get('/nl/functies/meertalig/untranslated-example')->assertNotFound();
    }

    public function test_it_seeds_the_whole_site(): void
    {
        // Five releases per package
        $this->assertSame(10, News::count());
        $this->assertSame(12, Event::count());
        $this->assertSame(8, Project::count());

        // Nine pages of its own plus an overview per content type
        $this->assertSame(12, Page::count());
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
            // A release with several changes lists them, one with a single change says
            // it in prose; everything else opens with a paragraph
            foreach (['nl', 'en'] as $locale) {
                $model === News::class
                    ? $this->assertMatchesRegularExpression('/^<(ul|p)>/', $section['body'][$locale])
                    : $this->assertStringStartsWith('<p>', $section['body'][$locale]);
            }
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

    /**
     * The chips above an overview come from the tags its own items carry, so one
     * shared vocabulary can serve three types without any of them offering a filter
     * that returns nothing. The releases tag themselves from the changelog's own
     * Added/Fixed/Changed headings.
     */
    public function test_each_overview_offers_tags_that_suit_it(): void
    {
        $tagsOf = fn (string $model): array => $model::with('tags')->get()
            ->flatMap(fn ($item) => $item->tags->map(fn ($tag): string => $tag->getTranslation('name', 'en')))
            ->unique()->sort()->values()->all();

        // 'Tests' comes from leap 1.0.0's own "### Tests" section: the seeder turns every
        // "### " heading into a chip, so a release that groups under something outside
        // Keep a Changelog's vocabulary widens this list.
        $this->assertSame(['Added', 'Changed', 'Fixed', 'Leap', 'Security', 'Template', 'Tests'], $tagsOf(News::class));
        $this->assertSame(['Content', 'Dates & times', 'Media'], $tagsOf(Event::class));
        $this->assertSame(['AI', 'Content', 'Media', 'Panel', 'Privacy'], $tagsOf(Project::class));
    }

    public function test_each_content_type_has_an_overview_its_items_hang_under(): void
    {
        foreach (['events', 'news', 'projects'] as $type) {
            $this->assertNotNull(PageController::overviewPage($type), "No overview page for {$type}.");
        }
    }

    /**
     * The overview page's slug is the base of every detail URL under it, and the demo
     * renames those overviews — so the path is read from the page rather than from the
     * translation of the type name.
     */
    public function test_an_item_detail_page_resolves(): void
    {
        $news = News::active()->first();

        $this->get('/'.PageController::overviewPage('news')->slug.'/'.$news->slug)
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
        $this->assertSame(3, $home->firstWhere('_name', 'news')['columns']);
        $this->assertSame(3, collect(PageController::overviewPage('news')->sections)->firstWhere('_name', 'news')['columns']);

        // And the child page sets the three that the home page never needs
        $options = collect(Page::find(3)->sections);

        $this->assertSame('past', $options->firstWhere('head.en', 'Period: past, in a grid of three')['period']);
        $this->assertSame(1, $options->firstWhere('head.en', 'Fixed to one tag')['tag']);
        $this->assertSame('horizontal', $options->firstWhere('head.en', 'A horizontal row, two in view')['layout']);
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
     * The three cases Event derives ends_at from, deliberately in the set rather than
     * left to chance: no times at all, a start without an end, and an end at or before
     * the start.
     */
    public function test_the_events_include_the_cases_the_model_handles(): void
    {
        $this->assertSame(2, Event::whereNull('start_time')->whereNull('end_time')->count(), 'All-day events, upcoming and past.');
        $this->assertSame(1, Event::whereNotNull('start_time')->whereNull('end_time')->count(), 'A start without an end.');

        $overnight = Event::whereColumn('end_time', '<=', 'start_time')->whereNotNull('end_time')->first();

        $this->assertNotNull($overnight, 'No event runs past midnight.');
        $this->assertTrue(
            $overnight->ends_at->isAfter($overnight->date->endOfDay()),
            'An event ending after midnight finishes the next day.',
        );
    }

    /**
     * Five releases are out and one is not: an item dated in the future is editable in
     * the panel and invisible on the site, which is the whole point of that field.
     */
    /**
     * A changelog only has an [Unreleased] section between releases, so the demo may
     * legitimately have none — right after a release it does not. What must hold
     * either way: an entry without a date is dated ahead and therefore hidden, and
     * everything the site does show is already out.
     */
    public function test_an_entry_without_a_release_date_is_hidden(): void
    {
        $ahead = News::where('published_at', '>', now())->count();

        $this->assertSame(News::count() - $ahead, News::active()->count());

        foreach (News::active()->get() as $release) {
            $this->assertTrue($release->published_at->isPast());
        }
    }

    /**
     * A release dated today is out — the changelog says so — but its noon timestamp
     * may not have arrived yet. A reset before 12:00 must clamp it to the reset's own
     * moment rather than hide today's release for hours, which on the demo host means
     * every deploy made in the morning.
     */
    public function test_a_release_dated_today_is_visible_after_a_morning_reset(): void
    {
        $newest = News::where('published_at', '<=', now())->orderByDesc('published_at')->first();

        // Re-seed at 09:00 on the newest release's own date, so that entry is "today"
        $this->travelTo($newest->published_at->copy()->startOfDay()->addHours(9));
        News::query()->delete();
        $this->seed(DemoSeeder::class);

        $today = News::active()->whereDate('published_at', now()->toDateString())->get();

        $this->assertNotEmpty($today, "Today's release is hidden after a morning reset.");

        foreach ($today as $release) {
            $this->assertLessThanOrEqual(now(), $release->published_at);
        }

        // The clamp must not break what the timestamps exist for: distinct values
        // in the file's own order
        $dates = News::orderByDesc('published_at')->pluck('published_at');

        $this->assertSame($dates->count(), $dates->unique()->count(), 'No two releases share a timestamp.');
    }

    /**
     * The releases are read from the package's own changelog rather than copied into
     * the seeder, so the demo is current on every deploy. Two releases can share a
     * date, so the file's order has to survive the database's sort.
     */
    public function test_the_releases_come_from_the_changelog(): void
    {
        $titles = News::orderByDesc('published_at')->pluck('title');

        // The title is the package and its version, nothing more: what the release did
        // is the intro, where it reads as the card's text. The newest may be an
        // [Unreleased] section, or an ordinary version when both packages have just
        // been released.
        $this->assertMatchesRegularExpression('/^(Leap|Template) (unreleased|\d+\.\d+\.\d+)$/', $titles->first());
        $this->assertMatchesRegularExpression('/^(Leap|Template) \d+\.\d+\.\d+$/', $titles->last());

        $this->assertNotEmpty(News::orderByDesc('published_at')->skip(1)->first()->intro);

        foreach (['Leap', 'Template'] as $package) {
            $this->assertNotEmpty($titles->filter(fn (string $t): bool => str_starts_with($t, $package)), "No releases of {$package}.");
        }

        $dates = News::orderByDesc('published_at')->pluck('published_at');

        $this->assertSame($dates->sortDesc()->values()->all(), $dates->all(), 'Strictly descending, same-day releases included.');
        $this->assertSame($dates->count(), $dates->unique()->count(), 'No two releases share a timestamp.');
    }

    public function test_projects_are_ordered_by_hand(): void
    {
        $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8], Project::orderBy('sort')->pluck('sort')->all());
    }

    /**
     * Str::slug drops the dots, so 1.1.10 and 1.11.0 would both become "1110" and
     * HasSlug would tell them apart with a -2 nobody can read. The seeder spells the
     * version out with dashes instead.
     */
    public function test_a_release_slug_keeps_the_version_readable(): void
    {
        $slugs = News::all()->map(fn (News $item): string => $item->getTranslation('slug', 'en', false));

        $this->assertSame($slugs->count(), $slugs->unique()->count(), 'Two releases share a slug.');

        foreach ($slugs as $slug) {
            $this->assertMatchesRegularExpression('/^(leap|template)-(unreleased|\d+(-\d+)+)$/', $slug);
        }

        // The very collision this guards against
        $this->assertNotSame(Str::slug('Leap 1.1.10'), Str::slug('Leap 1-1-10'));
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

        $this->assertCount(26, $used->filter(), 'Every content item names an image.');

        foreach ($used as $file) {
            $this->assertArrayHasKey($file, $manifest['images'], "{$file} is used but not in the manifest.");
        }

        // The images are reused across items on purpose — they are placeholders, and
        // one media record hanging off several records is worth showing
        $this->assertEqualsCanonicalizing(
            collect($manifest['images'])->keys()->filter(fn (string $path): bool => str_starts_with($path, 'abstract/'))->values()->all(),
            $used->unique()->values()->all(),
            'The items use exactly the card-sized images the manifest describes.',
        );
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

        // Sections gets a background rather than an inline image, and three files in
        // the one field of its gallery section
        $catalogue = Mediable::where('mediable_type', Page::class)->where('mediable_id', 2)->pluck('mediable_attribute');

        $this->assertContains('sections.2.background', $catalogue->all());
        $this->assertSame(3, $catalogue->filter(fn (string $a): bool => $a === 'sections.3.image')->count());

        // The legal pages are deliberately without
        $this->assertSame(0, Mediable::where('mediable_type', Page::class)->whereIn('mediable_id', [8, 9])->count());
    }

    protected function skipWithoutImages(): void
    {
        if (! File::exists(database_path('seeders/demo-images/abstract'))) {
            $this->markTestSkipped('No images generated yet — run demo:images.');
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
        // The demo account and the editor account, one role each
        $this->assertSame(2, DB::table('leap_role_user')->count());
        $this->assertSame(10, News::count());
        $this->assertSame(12, Event::count());
        $this->assertSame(8, Project::count());
        $this->assertSame(12, Page::count());
    }
}
