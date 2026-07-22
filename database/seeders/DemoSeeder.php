<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use NickDeKruijk\Leap\Models\Media;
use NickDeKruijk\Leap\Models\Mediable;
use NickDeKruijk\Leap\Models\Role;
use NickDeKruijk\Settings\Setting;

/**
 * The complete state of the public demo site at leap.nickdekruijk.nl: its admin
 * account, pages, tags and content. Run as the root seeder by demo:reset, which
 * rebuilds the site from scratch once the demo user has been idle long enough.
 *
 * Deliberately standalone. PageSeeder and the content seeders are stubs shipped by
 * leap-template and are left byte-identical to them, so the demo can carry its own
 * copy without drifting from what `leap:template` installs — and so a `--fresh`
 * never overwrites what the demo shows.
 *
 * Everything is dated relative to the moment of seeding rather than faked at
 * random, so every reset produces exactly the same site.
 */
class DemoSeeder extends Seeder
{
    /**
     * The demo's content types, in the order config('leap.content') registers them.
     * Their overview pages take id 7 and up and sort 100 and up.
     *
     * "overview" and "teaser" are extra keys for that type's section on its own
     * overview page and on the home page — the demo sets a few by hand so the site
     * shows more than one way of listing the same cards.
     */
    protected const OVERVIEWS = [
        7 => ['key' => 'events', 'title' => ['nl' => 'Agenda', 'en' => 'Events']],
        8 => ['key' => 'news', 'title' => ['nl' => 'Nieuws', 'en' => 'News'], 'overview' => ['columns' => 4], 'teaser' => ['columns' => 4]],
        9 => ['key' => 'projects', 'title' => ['nl' => 'Projecten', 'en' => 'Projects'], 'teaser' => ['layout' => 'grid']],
    ];

    /**
     * @var array<string, mixed>
     */
    protected array $content;

    public function run(): void
    {
        $this->content = require __DIR__.'/demo-content.php';

        $this->seedDemoUser();
        $this->seedTags();
        $this->seedPages();
        $this->seedPageImages();
        $this->seedOverviewPages();
        $this->seedNews();
        $this->seedEvents();
        $this->seedProjects();
        $this->seedSettings();
    }

    /**
     * The account demo visitors log in with. The role_user migration only attaches
     * the superuser role to the first user that exists while migrating, and on a
     * fresh database that is nobody, so it is attached here.
     */
    protected function seedDemoUser(): void
    {
        $user = User::firstOrCreate(
            ['email' => config('demo.email')],
            [
                'name' => 'Demo User',
                'password' => config('demo.password'),
            ],
        );

        Role::firstWhere('name', 'superuser')?->users()->syncWithoutDetaching([$user->id]);
    }

    protected function seedTags(): void
    {
        if (Tag::count() > 0) {
            return;
        }

        foreach ([
            ['nl' => 'Werkplaats', 'en' => 'Workshop'],
            ['nl' => 'Programma', 'en' => 'Programme'],
            ['nl' => 'Achtergrond', 'en' => 'Background'],
            ['nl' => 'Aankondiging', 'en' => 'Announcement'],
        ] as $sort => $name) {
            Tag::create(['name' => $name, 'sort' => $sort + 1]);
        }
    }

    protected function page(int $id, array $data): void
    {
        Page::updateOrCreate(['id' => $id], $data);
    }

    /**
     * The site's own pages. The home page carries a teaser row per content type
     * between its quote and its call to action; those rows only need the overview
     * to exist by the time a visitor opens the page, not while seeding.
     */
    protected function seedPages(): void
    {
        $this->page(1, [
            'title' => ['nl' => 'Home', 'en' => 'Home'],
            // The reserved slug "/" resolves order-independently and keeps the home
            // page from also being reachable under a second URL.
            'slug' => ['nl' => '/', 'en' => '/'],
            'menuitem' => false,
            'sort' => 1,
            'description' => [
                'nl' => 'Een open werkplaats voor druktechniek, hout en reparatie — en de demo van het Leap adminpaneel.',
                'en' => 'An open workshop for printing, wood and repair — and the demo of the Leap admin panel.',
            ],
            'sections' => [
                [
                    '_name' => 'slide',
                    '_sort' => 0,
                    'active' => true,
                    // Without an uploaded image the slides fall back to a dark gradient,
                    // so they ask for white text rather than leaning on the default.
                    'white_text' => true,
                    'head' => ['nl' => 'De werkplaats is open', 'en' => 'The workshop is open'],
                    'body' => ['nl' => '<p>Elke donderdagavond kun je zonder aanmelding binnenlopen.</p>', 'en' => '<p>Every Thursday evening you can walk in without booking.</p>'],
                ],
                [
                    '_name' => 'slide',
                    '_sort' => 1,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['nl' => 'Leren door te maken', 'en' => 'Learning by making'],
                    'body' => ['nl' => '<p>Workshops in zeefdruk, boekbinden en houtbewerking, het hele jaar door.</p>', 'en' => '<p>Workshops in screen printing, bookbinding and woodwork, all year round.</p>'],
                ],
                [
                    '_name' => 'slide',
                    '_sort' => 2,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['nl' => 'Alles zelf te beheren', 'en' => 'All of it self-managed'],
                    'body' => ['nl' => '<p>Deze hele website wordt beheerd in het Leap adminpaneel. Log in en probeer het.</p>', 'en' => '<p>This entire website is managed in the Leap admin panel. Log in and try it.</p>'],
                ],
                // One "default" section per image_position, so the home page shows every
                // layout at once. Upload an image per section in the admin panel; without
                // one the text spans the full width.
                [
                    '_name' => 'default',
                    '_sort' => 3,
                    'active' => true,
                    'head' => ['nl' => 'Wie hier komen', 'en' => 'Who comes here'],
                    'image_position' => 'left',
                    'body' => ['nl' => '<p>Ontwerpers, buurtbewoners, scholen en iedereen die iets kapot heeft dat nog te redden is.</p>', 'en' => '<p>Designers, neighbours, schools and anyone with something broken that can still be saved.</p>'],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 4,
                    'active' => true,
                    'head' => ['nl' => 'Wat er staat', 'en' => 'What we have'],
                    'image_position' => 'right',
                    'body' => ['nl' => '<p>Twee zeefdrukpersen, een risograaf, een houtwerkplaats en een muur vol gereedschap.</p>', 'en' => '<p>Two screen printing presses, a risograph, a wood workshop and a wall full of tools.</p>'],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 5,
                    'active' => true,
                    'head' => ['nl' => 'Zelf aan de slag', 'en' => 'Get to work yourself'],
                    'image_position' => 'bottom wide',
                    'body' => ['nl' => '<p>Een dagdeel machinetijd kost tien euro, inclusief uitleg van een vrijwilliger.</p>', 'en' => '<p>Half a day of machine time costs ten euros, including instruction from a volunteer.</p>'],
                ],
                [
                    '_name' => 'quote',
                    '_view' => 'sections.default',
                    '_sort' => 6,
                    'active' => true,
                    'head' => ['nl' => 'Ik kwam voor een kapotte lamp en ging weg met een affiche.', 'en' => 'I came for a broken lamp and left with a poster.'],
                    'body' => ['nl' => 'Bezoeker van het repair café', 'en' => 'Visitor to the repair café'],
                ],
                ...$this->homeTeaserSections(),
                [
                    '_name' => 'cta',
                    '_view' => 'sections.default',
                    '_sort' => 13,
                    'active' => true,
                    'head' => ['nl' => 'Langskomen?', 'en' => 'Drop by?'],
                    'body' => ['nl' => '<p><a class="button" href="/contact">Neem contact op</a></p>', 'en' => '<p><a class="button" href="/en/contact">Get in touch</a></p>'],
                ],
            ],
        ]);

        $this->page(2, [
            'title' => ['nl' => 'Over ons', 'en' => 'About us'],
            'slug' => ['nl' => 'over-ons', 'en' => 'about-us'],
            // High sort so About and Contact trail the content-type overviews (sort 100
            // and up) at the end of the menu, whatever order the pages are seeded in.
            'sort' => 200,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'head' => ['nl' => 'Zeven jaar werkplaats', 'en' => 'Seven years of workshop'],
                    'image_position' => 'left',
                    'body' => [
                        'nl' => '<p>Wat begon als een gedeelde garage met één zeefdrukpers is uitgegroeid tot een hal waar zo\'n honderd mensen per week iets maken, repareren of leren.</p><p>De werkplaats draait op vrijwilligers en op de opbrengst van workshops. Alles wat hier staat is geschonken, geleend of gevonden.</p>',
                        'en' => '<p>What started as a shared garage with a single screen printing press has grown into a hall where some hundred people a week make, repair or learn something.</p><p>The workshop runs on volunteers and on what the workshops bring in. Everything here was donated, borrowed or found.</p>',
                    ],
                ],
            ],
        ]);

        $this->page(3, [
            'title' => ['nl' => 'Diensten', 'en' => 'Services'],
            'parent' => 2,
            'slug' => ['nl' => 'diensten', 'en' => 'services'],
            'sort' => 1,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'head' => ['nl' => 'Wat we voor je kunnen doen', 'en' => 'What we can do for you'],
                    'body' => [
                        'nl' => '<p>Naast het open programma drukken we in opdracht: affiches, uitnodigingen en kleine oplagen waarvoor een drukkerij te groot is.</p><p>Ook ontwerpen we bewegwijzering en huisstijlen voor organisaties in de buurt.</p>',
                        'en' => '<p>Alongside the open programme we print to order: posters, invitations and small runs a commercial printer is too big for.</p><p>We also design signage and identities for organisations nearby.</p>',
                    ],
                ],
            ],
        ]);

        $this->page(4, [
            'title' => ['nl' => 'Contact', 'en' => 'Contact'],
            'slug' => ['nl' => 'contact', 'en' => 'contact'],
            'sort' => 210,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'head' => ['nl' => 'Langskomen of schrijven', 'en' => 'Visit or write'],
                    'body' => [
                        'nl' => '<p>Voorbeeldstraat 1, 3511 AB Utrecht. Open op donderdagavond en zaterdagmiddag, en op afspraak.</p><p>Mailen kan naar info@example.com — we lezen mee op werkdagen.</p>',
                        // The country is worth spelling out for a reader who needs it;
                        // in Dutch it would only state the obvious.
                        'en' => '<p>Voorbeeldstraat 1, 3511 AB Utrecht, The Netherlands. Open on Thursday evenings and Saturday afternoons, and by appointment.</p><p>Write to info@example.com — we read along on working days.</p>',
                    ],
                ],
            ],
        ]);

        // Legal pages, linked from the footer but kept out of the main navigation
        $this->page(5, [
            'title' => ['nl' => 'Privacybeleid', 'en' => 'Privacy policy'],
            'slug' => ['nl' => 'privacy', 'en' => 'privacy'],
            'menuitem' => false,
            'sort' => 4,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'head' => ['nl' => 'Privacybeleid', 'en' => 'Privacy policy'],
                    'body' => [
                        'nl' => '<p>Dit is een demo-website. Wat je hier invult wordt niet bewaard: de database wordt een kwartier na de laatste wijziging volledig teruggezet.</p>',
                        'en' => '<p>This is a demo website. Whatever you enter here is not kept: the database is fully restored a quarter of an hour after the last change.</p>',
                    ],
                ],
                // The cookie table renders config('leap.consent'), so the privacy page
                // cannot drift away from the cookies the site actually sets.
                [
                    '_name' => 'cookies',
                    '_sort' => 1,
                    'active' => true,
                    'head' => ['nl' => 'Cookies', 'en' => 'Cookies'],
                    'body' => [
                        'nl' => '<p>Hieronder staat welke cookies deze website gebruikt, waarvoor, en hoe lang ze bewaard worden.</p>',
                        'en' => '<p>Below is every cookie this website uses, what for, and how long it is kept.</p>',
                    ],
                ],
            ],
        ]);

        $this->page(6, [
            'title' => ['nl' => 'Algemene voorwaarden', 'en' => 'Terms & conditions'],
            'slug' => ['nl' => 'algemene-voorwaarden', 'en' => 'terms'],
            'menuitem' => false,
            'sort' => 5,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'head' => ['nl' => 'Algemene voorwaarden', 'en' => 'Terms & conditions'],
                    'body' => [
                        'nl' => '<p>Deelname aan een workshop is op eigen risico. Afmelden kan tot 48 uur van tevoren kosteloos.</p>',
                        'en' => '<p>Taking part in a workshop is at your own risk. Cancelling is free of charge up to 48 hours in advance.</p>',
                    ],
                ],
            ],
        ]);
    }

    /**
     * The photographs on the pages, keyed by the array position of the section they
     * belong to. Home's first six sections are its three slides and the three text
     * sections that demonstrate the image positions; the teasers below them show the
     * items' own images, and the legal pages stay without.
     */
    protected function seedPageImages(): void
    {
        $this->attachSectionImages(Page::find(1), [
            0 => 'pages/home-slide-open.jpg',
            1 => 'pages/home-slide-leren.jpg',
            2 => 'pages/home-slide-beheer.jpg',
            3 => 'pages/home-wie-hier-komen.jpg',
            4 => 'pages/home-wat-er-staat.jpg',
            5 => 'pages/home-zelf-aan-de-slag.jpg',
        ]);

        $this->attachSectionImages(Page::find(2), [0 => 'pages/over-ons.jpg']);
        $this->attachSectionImages(Page::find(3), [0 => 'pages/diensten.jpg']);
        $this->attachSectionImages(Page::find(4), [0 => 'pages/contact.jpg']);
    }

    /**
     * The teaser rows on the home page: the same section a content type renders on
     * its overview, but limited to a handful of items.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function homeTeaserSections(): array
    {
        return collect(self::OVERVIEWS)->values()
            ->map(fn (array $overview, int $index): array => [
                '_name' => $overview['key'],
                '_view' => 'sections.items',
                '_sort' => 7 + $index,
                'active' => true,
                'head' => $overview['title'],
                'limit' => 6,
                ...$overview['teaser'] ?? [],
            ])->all();
    }

    /**
     * One page per content type. An unlimited, untagged items section is what makes
     * a page the canonical overview (see PageController::overviewPage()) and gives
     * every item of that type its detail URL.
     */
    protected function seedOverviewPages(): void
    {
        $position = 0;

        foreach (self::OVERVIEWS as $id => $overview) {
            $this->page($id, [
                'active' => true,
                'menuitem' => true,
                'title' => $overview['title'],
                'sort' => 100 + $position++,
                'sections' => [[
                    '_name' => $overview['key'],
                    '_view' => 'sections.items',
                    '_sort' => 0,
                    'active' => true,
                    'head' => $overview['title'],
                    ...$overview['overview'] ?? [],
                ]],
            ]);
        }
    }

    protected function seedNews(): void
    {
        if (News::count() > 0) {
            return;
        }

        foreach ($this->content['news'] as $index => $item) {
            $news = News::create($this->item($item) + [
                // Spread backwards from a fortnight ago, so the overview reads the same
                // after every reset and nothing is dated in the future.
                'published_at' => now()->subDays(2)->subWeeks($index),
            ]);

            $this->attachTags($news, $index);
            $this->attachImage($news, $item['image'] ?? null, 'images', $item['title']);
        }
    }

    protected function seedEvents(): void
    {
        if (Event::count() > 0) {
            return;
        }

        // Times are as deliberate as the dates: the last two upcoming events are the
        // cases the model has rules for — one that never says when it stops, and one
        // that ends after midnight and therefore finishes the next day.
        $upcoming = [
            ['start_time' => '10:00', 'end_time' => '17:00'],
            ['start_time' => '13:30', 'end_time' => '17:00'],
            ['start_time' => '20:00', 'end_time' => '21:30'],
            ['start_time' => '10:00', 'end_time' => '14:00'],
            ['start_time' => '17:00', 'end_time' => null],
            ['start_time' => '20:00', 'end_time' => '02:30'],
        ];

        foreach ($this->content['events']['future'] as $index => $item) {
            $event = Event::create($this->item($item) + $upcoming[$index] + [
                'date' => now()->addWeeks($index + 1)->startOfDay(),
            ]);

            $this->attachTags($event, $index);
            $this->attachImage($event, $item['image'] ?? null, 'images', $item['title']);
        }

        // Finished events are pinned to the day of the year their copy talks about, so
        // a spring market is never dated to November whatever month the demo resets in.
        $finished = [
            ['month' => 4, 'day' => 12, 'start_time' => '11:00', 'end_time' => '17:00'],
            ['month' => 3, 'day' => 8, 'start_time' => '13:00', 'end_time' => '16:30'],
            ['month' => 10, 'day' => 3, 'start_time' => '09:30', 'end_time' => '12:00'],
            ['month' => 9, 'day' => 19, 'start_time' => '20:00', 'end_time' => '22:00'],
            ['month' => 12, 'day' => 5, 'start_time' => '12:00', 'end_time' => '18:00'],
            ['month' => 1, 'day' => 6, 'start_time' => '09:00', 'end_time' => '11:00'],
        ];

        foreach ($this->content['events']['past'] as $index => $item) {
            $event = Event::create($this->item($item) + [
                'date' => $this->mostRecent($finished[$index]['month'], $finished[$index]['day']),
                'start_time' => $finished[$index]['start_time'],
                'end_time' => $finished[$index]['end_time'],
            ]);

            $this->attachTags($event, $index + 3);
            $this->attachImage($event, $item['image'] ?? null, 'images', $item['title']);
        }
    }

    protected function seedProjects(): void
    {
        if (Project::count() > 0) {
            return;
        }

        foreach ($this->content['projects'] as $index => $item) {
            $project = Project::create($this->item($item) + ['sort' => $index + 1]);

            $this->attachTags($project, $index);
            $this->attachImage($project, $item['image'] ?? null, 'images', $item['title']);
        }
    }

    /**
     * The columns every content type shares: what a visitor reads on the card and on
     * the detail page. The one text section has an image position but no image —
     * upload one in the admin panel and the layout is there waiting for it.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function item(array $item): array
    {
        return [
            'active' => true,
            'title' => $item['title'],
            'intro' => $item['intro'],
            'sections' => [[
                '_name' => 'default',
                '_sort' => 0,
                'active' => true,
                'image_position' => 'right',
                'head' => $item['head'],
                'body' => $item['body'],
            ]],
        ];
    }

    /**
     * Put a generated photograph on the public disk and hang it off a model, the way
     * the editor's own syncMedia() does: nothing goes into the sections JSON, the
     * attachment is a leap_mediables row naming the attribute it belongs to
     * ('images' for a whole item, 'sections.{key}.image' for one section of a page).
     *
     * Missing source file is not an error. The images are generated once by
     * demo:images and committed, but a clone without them still has to seed a
     * working site — it simply gets the placeholder gradients instead.
     *
     * @param  array<string, string>  $alt  Alt text per locale
     */
    protected function attachImage(Model $model, ?string $file, string $attribute, array $alt = []): void
    {
        if (! $file || ! File::exists($source = database_path('seeders/demo-images/'.$file))) {
            return;
        }

        Storage::disk('public')->put($file, File::get($source));

        $media = Media::forFile($file);

        if (! $media) {
            return;
        }

        if ($alt) {
            // meta is cast but not fillable, so it is assigned rather than created with
            $media->meta = ['alt' => $alt] + ($media->meta ?? []);
            $media->save();
        }

        Mediable::where('mediable_type', $model::class)
            ->where('mediable_id', $model->id)
            ->where('mediable_attribute', $attribute)
            ->delete();

        Mediable::create([
            'media_id' => $media->id,
            'mediable_type' => $model::class,
            'mediable_id' => $model->id,
            'mediable_attribute' => $attribute,
            'sort' => 0,
        ]);
    }

    /**
     * The photographs on a page, keyed by the array position of the section they
     * belong to — the key in the sections JSON, not its _sort.
     *
     * @param  array<int, string>  $images
     */
    protected function attachSectionImages(Page $page, array $images): void
    {
        // Read the raw column rather than the accessor: that one wraps every section
        // in an ArrayObject and sorts by _sort, while the attribute name has to carry
        // the array key the section is stored under.
        $sections = json_decode($page->getRawOriginal('sections') ?: '[]', true);

        foreach ($images as $index => $file) {
            $this->attachImage($page, $file, "sections.{$index}.image", $sections[$index]['head'] ?? []);
        }
    }

    /**
     * One or two tags per item, rotating through the set rather than drawn at random
     * so the filter chips above each overview hold the same items after a reset.
     */
    protected function attachTags(News|Event|Project $item, int $index): void
    {
        $tags = Tag::orderBy('sort')->pluck('id');

        if ($tags->isEmpty()) {
            return;
        }

        $ids = [$tags[$index % $tags->count()]];

        if ($index % 3 !== 0) {
            $ids[] = $tags[($index + 2) % $tags->count()];
        }

        $item->tags()->sync($ids);
    }

    /**
     * The most recent occurrence of a day of the year, this year or last. Today
     * counts as last year's: an event held earlier today has not finished yet at
     * every hour of the day, and these are the ones that have to be over.
     */
    protected function mostRecent(int $month, int $day): Carbon
    {
        $date = Carbon::create(now()->year, $month, $day)->startOfDay();

        return $date->greaterThanOrEqualTo(now()->startOfDay()) ? $date->subYear() : $date;
    }

    /**
     * Footer settings, when the settings package is installed. socials and
     * footer_links take "label:url" per line (the ':' key separator of setting_array()).
     */
    protected function seedSettings(): void
    {
        if (! class_exists(Setting::class)) {
            return;
        }

        Setting::set([
            'html_head' => [
                'value' => '',
                'description' => 'Code direct vóór </head>. Alleen voor code die GÉÉN toestemming vereist — trackers horen in de scripts_-instellingen, anders draaien ze buiten het cookiesysteem om.',
            ],
            'scripts_analytics' => [
                'value' => '',
                'description' => 'Statistiek-scripts (bijv. Google Analytics): plak hier de code van de leverancier. Draait pas nadat de bezoeker statistieken heeft toegestaan.',
            ],
            'scripts_embeds' => [
                'value' => '',
                'description' => 'Scripts voor ingesloten inhoud. Draait pas nadat de bezoeker die inhoud toestaat.',
            ],
            'footer_contact' => [
                'value' => "Voorbeeldstraat 1\n3511 AB Utrecht\ninfo@example.com",
                'description' => 'Adres/contactgegevens in de footer',
            ],
            'socials' => [
                'value' => "instagram:https://instagram.com\nlinkedin:https://linkedin.com\nfacebook:https://facebook.com",
                'description' => 'Social media, één "naam:url" per regel (naam = FontAwesome brand-icoon)',
            ],
            'footer_copyright' => [
                'value' => '© '.date('Y').' '.config('app.name'),
                'description' => 'Copyright-regel onderaan de footer',
            ],
            'footer_links' => [
                'value' => "Privacy:/privacy\nAlgemene voorwaarden:/algemene-voorwaarden",
                'description' => 'Footer-links, één "label:url" per regel',
            ],
            'og_image' => [
                'value' => '',
                'description' => 'Standaard social-share afbeelding (URL of /storage-pad); pagina-eigen afbeelding gaat voor',
            ],
        ]);
    }
}
