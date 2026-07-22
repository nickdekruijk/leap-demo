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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NickDeKruijk\Leap\Livewire\FileManager;
use NickDeKruijk\Leap\Livewire\Profile;
use NickDeKruijk\Leap\Models\Media;
use NickDeKruijk\Leap\Models\Mediable;
use NickDeKruijk\Leap\Models\Role;
use NickDeKruijk\Settings\Setting;

/**
 * The complete state of the public demo at leap.nickdekruijk.nl. Run as the root
 * seeder by demo:reset, which rebuilds the site once the demo user has been idle.
 *
 * The demo documents itself: every page shows a capability and explains where to
 * change it, because this is a starting point for your own project — a leapstart —
 * and not a finished website. Content that only exists to demonstrate something says
 * so, and invites you to replace it.
 *
 * Deliberately standalone. PageSeeder and the content seeders are stubs shipped by
 * leap-template and stay byte-identical to them, so the demo can carry its own copy
 * without drifting from what `leap:template` installs.
 *
 * Everything is dated relative to the moment of seeding rather than faked at random,
 * so every reset produces exactly the same site.
 */
class DemoSeeder extends Seeder
{
    /**
     * The content types, in the order config('leap.content') registers them. Their
     * overview pages take id 10 and up and sort 100 and up.
     *
     * "overview" and "teaser" are extra keys for that type's section on its own
     * overview page and on the home page.
     */
    protected const OVERVIEWS = [
        10 => ['key' => 'events', 'title' => ['en' => 'Agenda', 'nl' => 'Agenda']],
        11 => [
            'key' => 'news',
            'title' => ['en' => 'Releases', 'nl' => 'Releases'],
            'overview' => ['columns' => 3],
            'teaser' => ['columns' => 3],
            'intro' => [[
                '_name' => 'default',
                '_sort' => 0,
                'active' => true,
                'image_position' => 'none',
                'head' => ['en' => 'Straight from the changelog', 'nl' => 'Rechtstreeks uit de changelog'],
                'body' => [
                    'en' => '<p>Read from the <code>CHANGELOG.md</code> of both packages every time the demo is seeded, so they cannot fall behind a release. Filter by <em>Leap</em> for the admin panel or <em>Template</em> for the website scaffolding, or by what a release did: added, fixed, changed.</p><p>They are in English, because release notes are not translated.</p>',
                    'nl' => '<p>Bij elke seed gelezen uit de <code>CHANGELOG.md</code> van beide packages, zodat ze nooit achterlopen op een release. Filter op <em>Leap</em> voor het adminpaneel of <em>Template</em> voor de website-scaffolding, of op wat een release deed: added, fixed, changed.</p><p>Ze zijn Engelstalig, want release notes worden niet vertaald.</p>',
                ],
            ]],
        ],
        12 => [
            'key' => 'projects',
            'title' => ['en' => 'Reference', 'nl' => 'Naslag'],
            'teaser' => ['layout' => 'grid'],
            // An overview page is an ordinary page, so it can say something before its
            // cards. This is where the three content archetypes are explained.
            'intro' => [[
                '_name' => 'default',
                '_sort' => 0,
                'active' => true,
                'image_position' => 'none',
                'head' => ['en' => 'Short definitions', 'nl' => 'Korte definities'],
                'body' => [
                    'en' => '<p>One card per concept, kept deliberately brief. They are records of the third content archetype: no dates, ordered by hand, dragged into place in the panel — where the other two order themselves by a publication date and by an event date.</p>',
                    'nl' => '<p>Eén kaart per begrip, bewust kort gehouden. Het zijn records van het derde content-archetype: geen datums, met de hand geordend, in het paneel op hun plek gesleept — waar de andere twee zichzelf ordenen op publicatiedatum en op eventdatum.</p>',
                ],
            ]],
        ],
    ];

    /**
     * @var array<string, mixed>
     */
    protected array $content;

    public function run(): void
    {
        $this->content = require __DIR__.'/demo-content.php';

        $this->seedRoles();
        $this->seedUsers();
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
     * Two roles, because one role demonstrates nothing. The editor may work on
     * content and files and has no access at all to users and roles — those modules
     * are not greyed out for them, they are simply not there.
     */
    protected function seedRoles(): void
    {
        Role::firstOrCreate(['name' => 'editor'], [
            'permissions' => [
                ['_name' => \App\Leap\Page::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => false],
                ['_name' => \App\Leap\News::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => true],
                ['_name' => \App\Leap\Event::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => true],
                ['_name' => \App\Leap\Project::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => true],
                ['_name' => \App\Leap\Tag::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => false],
                ['_name' => FileManager::class, 'read' => true, 'create' => true, 'update' => true, 'delete' => true],
                ['_name' => Profile::class, 'read' => true, 'update' => true],
            ],
        ]);
    }

    /**
     * The account demo visitors log in with, plus the restricted one they can compare
     * it against. The role_user migration only attaches superuser to the first user
     * that exists while migrating, and on a fresh database that is nobody.
     */
    protected function seedUsers(): void
    {
        $demo = User::firstOrCreate(
            ['email' => config('demo.email')],
            ['name' => 'Demo User', 'password' => config('demo.password')],
        );

        Role::firstWhere('name', 'superuser')?->users()->syncWithoutDetaching([$demo->id]);

        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            ['name' => 'Editor', 'password' => config('demo.password')],
        );

        Role::firstWhere('name', 'editor')?->users()->syncWithoutDetaching([$editor->id]);
    }

    /**
     * One shared vocabulary for all three content types. Each overview only shows the
     * chips its own items carry, so the agenda offers a different set from the
     * releases without any of them needing a vocabulary of their own.
     *
     * The changelog's own section headings — Added, Fixed, Changed — are added by the
     * release parser as it meets them, which is why they are not listed here.
     */
    protected const TAGS = [
        'Content' => ['en' => 'Content', 'nl' => 'Content'],
        'Dates & times' => ['en' => 'Dates & times', 'nl' => 'Datum en tijd'],
        'Media' => ['en' => 'Media', 'nl' => 'Media'],
        'Panel' => ['en' => 'Panel', 'nl' => 'Paneel'],
        'AI' => ['en' => 'AI', 'nl' => 'AI'],
        'Privacy' => ['en' => 'Privacy', 'nl' => 'Privacy'],
    ];

    protected function seedTags(): void
    {
        foreach (array_values(self::TAGS) as $sort => $name) {
            Tag::firstOrCreate(['name->en' => $name['en']], ['name' => $name, 'sort' => $sort + 1]);
        }
    }

    protected function page(int $id, array $data): void
    {
        Page::updateOrCreate(['id' => $id], $data);
    }

    /**
     * The pages. Home carries a teaser row per content type between its quote and its
     * call to action; the rest each demonstrate one part of the template.
     */
    protected function seedPages(): void
    {
        $this->page(1, [
            'title' => ['en' => 'Home', 'nl' => 'Home'],
            // The reserved slug "/" resolves order-independently and keeps the home
            // page from also being reachable under a second URL.
            'slug' => ['en' => '/', 'nl' => '/'],
            'menuitem' => false,
            'sort' => 1,
            'description' => [
                'en' => 'The demo of Leap, a Laravel admin panel, and of the website template that comes with it. Everything you see here is editable, and resets itself every quarter of an hour.',
                'nl' => 'De demo van Leap, een Laravel adminpaneel, en van de websitetemplate die erbij hoort. Alles wat je hier ziet is aanpasbaar en zet zichzelf elk kwartier terug.',
            ],
            'sections' => [
                [
                    '_name' => 'slide',
                    '_sort' => 0,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['en' => 'Laravel Easy Admin Panel', 'nl' => 'Laravel Easy Admin Panel'],
                    'body' => [
                        'en' => '<p>Declare a screen in PHP and Leap gives you the index, the editor, validation, search and permissions.</p>',
                        'nl' => '<p>Declareer een scherm in PHP en Leap geeft je de index, de editor, validatie, zoeken en rechten.</p>',
                    ],
                ],
                [
                    '_name' => 'slide',
                    '_sort' => 1,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['en' => 'A leapstart for your next website', 'nl' => 'Een leapstart voor je volgende website'],
                    'body' => [
                        'en' => '<p>One command scaffolds the frontend too: pages, sections, navigation, search, SEO and cookie consent.</p>',
                        'nl' => '<p>Eén commando zet ook de frontend neer: pagina\'s, secties, navigatie, zoeken, SEO en cookietoestemming.</p>',
                    ],
                ],
                [
                    '_name' => 'slide',
                    '_sort' => 2,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['en' => 'Change anything you like', 'nl' => 'Verander wat je wilt'],
                    'body' => [
                        'en' => '<p>Log in with <strong>info@example.com</strong> / <strong>leapdemo</strong>. The site resets 15 minutes after the last edit.</p>',
                        'nl' => '<p>Log in met <strong>info@example.com</strong> / <strong>leapdemo</strong>. De site reset 15 minuten na de laatste wijziging.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 3,
                    'active' => true,
                    'menuitem' => true,
                    'head' => ['en' => 'A screen per model', 'nl' => 'Een scherm per model'],
                    'image_position' => 'left',
                    'body' => [
                        'en' => '<p>A resource is one class pointing at one model. Fields are declared with a fluent API — <code>->translatable()</code>, <code>->required()</code>, <code>->media()</code>, <code>->searchable()</code> — and the panel builds the rest. <code>leap:module</code> writes the first version from your migration.</p>',
                        'nl' => '<p>Een resource is één class die naar één model wijst. Velden declareer je met een vloeiende API — <code>->translatable()</code>, <code>->required()</code>, <code>->media()</code>, <code>->searchable()</code> — en het paneel bouwt de rest. <code>leap:module</code> schrijft de eerste versie op basis van je migratie.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 4,
                    'active' => true,
                    'head' => ['en' => 'Pages an editor composes', 'nl' => 'Pagina\'s die een redacteur samenstelt'],
                    'image_position' => 'right',
                    'body' => [
                        'en' => '<p>This page is a list of sections: three slides, these text blocks, a quote, three card rows and a call to action. You decide which types exist; the editor adds, reorders and switches them off. Have a look at <em>Sections</em> for all of them at once.</p>',
                        'nl' => '<p>Deze pagina is een lijst secties: drie slides, deze tekstblokken, een citaat, drie kaartenrijen en een call to action. Jij bepaalt welke types bestaan; de redacteur voegt toe, sorteert en zet uit. Kijk op <em>Secties</em> voor allemaal tegelijk.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 5,
                    'active' => true,
                    'head' => ['en' => 'Two languages, one record', 'nl' => 'Twee talen, één record'],
                    'image_position' => 'bottom wide',
                    'body' => [
                        'en' => '<p>Mark an attribute translatable and the editor grows language tabs. English is served unprefixed here and Dutch under <code>/nl</code>; slugs, menus, hreflang and the sitemap all follow from that one setting.</p>',
                        'nl' => '<p>Markeer een attribuut als vertaalbaar en de editor krijgt taaltabs. Engels staat hier op de kale URL\'s en Nederlands onder <code>/nl</code>; slugs, menu\'s, hreflang en de sitemap volgen uit die ene instelling.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 6,
                    'active' => true,
                    // No image at all: image_position 'none' centres the text column
                    'image_position' => 'none',
                    'head' => ['en' => 'A starting point, not a finished site', 'nl' => 'Een startpunt, geen afgeronde site'],
                    'body' => [
                        'en' => '<p>Everything here exists to be replaced. The copy explains the feature it is sitting in, the images are deliberately abstract placeholders, and the styling is a handful of design tokens in <code>project.scss</code>. Delete what you do not need and build your own thing on top.</p>',
                        'nl' => '<p>Alles hier bestaat om vervangen te worden. De teksten leggen uit in welke functie ze zitten, de beelden zijn bewust abstracte placeholders, en de vormgeving is een handvol design tokens in <code>project.scss</code>. Gooi weg wat je niet nodig hebt en bouw er je eigen ding bovenop.</p>',
                    ],
                ],
                [
                    '_name' => 'quote',
                    '_view' => 'sections.default',
                    '_sort' => 7,
                    'active' => true,
                    'head' => ['en' => 'The best starting point is one you can throw half of away.', 'nl' => 'Het beste startpunt is er een waarvan je de helft kunt weggooien.'],
                    'body' => ['en' => 'The quote section: a heading and an attribution', 'nl' => 'De citaat-sectie: een kop en een bronvermelding'],
                ],
                ...$this->homeTeaserSections(),
                [
                    '_name' => 'cta',
                    '_view' => 'sections.default',
                    '_sort' => 13,
                    'active' => true,
                    'head' => ['en' => 'Start your own', 'nl' => 'Begin je eigen site'],
                    'body' => [
                        'en' => '<p><a class="button" href="/get-started">Two commands and you are running</a></p>',
                        'nl' => '<p><a class="button" href="/nl/aan-de-slag">Twee commando\'s en je draait</a></p>',
                    ],
                ],
            ],
        ]);

        $this->page(2, [
            'title' => ['en' => 'Sections', 'nl' => 'Secties'],
            'html_title' => ['en' => 'Every section type, rendered — Leap demo', 'nl' => 'Elk sectietype, gerenderd — Leap demo'],
            'description' => [
                'en' => 'Every section type the template ships with, one after another, each explaining what it is and which fields it has.',
                'nl' => 'Elk sectietype dat de template meelevert, één voor één, elk met uitleg over wat het is en welke velden het heeft.',
            ],
            'parent' => 4,
            'sort' => 1,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'What a section is', 'nl' => 'Wat een sectie is'],
                    'body' => [
                        'en' => '<p>A page stores its sections as json. Each type is declared once in <code>app/Leap/Concerns/ContentSections.php</code> with the fields it offers, and rendered by a Blade view of the same name. Everything below is one of them.</p>',
                        'nl' => '<p>Een pagina bewaart zijn secties als json. Elk type wordt één keer gedeclareerd in <code>app/Leap/Concerns/ContentSections.php</code> met de velden die het biedt, en gerenderd door een gelijknamige Blade-view. Alles hieronder is er zo een.</p>',
                    ],
                ],
                [
                    '_name' => 'slide',
                    '_sort' => 1,
                    'active' => true,
                    'white_text' => true,
                    'head' => ['en' => 'Slide', 'nl' => 'Slide'],
                    'body' => [
                        'en' => '<p>A run of slides becomes a carousel; a single one is just a full-width header. Give it an .mp4 instead of an image and it plays muted on a loop.</p>',
                        'nl' => '<p>Een reeks slides wordt een carrousel; één slide is gewoon een volle-breedte header. Geef hem een .mp4 in plaats van een afbeelding en hij speelt gedempt in een lus.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 2,
                    'active' => true,
                    'menuitem' => true,
                    'white_text' => true,
                    'head' => ['en' => 'Text with a background photo', 'nl' => 'Tekst met een achtergrondfoto'],
                    'image_position' => 'none',
                    'body' => [
                        'en' => '<p>The same text section, now with a full-bleed background and white text. Both are switches in the editor: <em>background photo</em> and <em>white text</em>. This one also has <em>menu item</em> on, which is why it appears as an anchor under Sections in the navigation.</p>',
                        'nl' => '<p>Dezelfde tekstsectie, nu met een volledige achtergrond en witte tekst. Beide zijn schakelaars in de editor: <em>achtergrondfoto</em> en <em>witte tekst</em>. Bij deze staat ook <em>menu-item</em> aan, en daarom verschijnt hij als anker onder Secties in de navigatie.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 3,
                    'active' => true,
                    'menuitem' => true,
                    'head' => ['en' => 'A gallery in one section', 'nl' => 'Een galerij in één sectie'],
                    'image_position' => 'right',
                    'body' => [
                        'en' => '<p>The image field takes more than one file. Pick a second and a third and they stack in the image column — no separate gallery section, no extra type to maintain.</p>',
                        'nl' => '<p>Het afbeeldingsveld neemt meer dan één bestand. Kies een tweede en een derde en ze stapelen in de beeldkolom — geen aparte galerij-sectie, geen extra type om te onderhouden.</p>',
                    ],
                ],
                [
                    '_name' => 'video',
                    '_sort' => 4,
                    'active' => true,
                    'head' => ['en' => 'Video', 'nl' => 'Video'],
                    'video_id' => 'dQw4w9WgXcQ',
                    'body' => [
                        'en' => '<p>Paste a YouTube id or a Vimeo number. The poster is fetched from the provider once and stored locally, and the player only loads after the visitor allows embedded content — so nothing third-party runs before consent.</p>',
                        'nl' => '<p>Plak een YouTube-id of Vimeo-nummer. De poster wordt één keer bij de aanbieder opgehaald en lokaal bewaard, en de speler laadt pas nadat de bezoeker ingesloten inhoud toestaat — er draait dus niets van derden vóór toestemming.</p>',
                    ],
                ],
                [
                    '_name' => 'quote',
                    '_view' => 'sections.default',
                    '_sort' => 5,
                    'active' => true,
                    'head' => ['en' => 'A section type is a name, a few fields and a Blade view.', 'nl' => 'Een sectietype is een naam, een paar velden en een Blade-view.'],
                    'body' => ['en' => 'Quote section', 'nl' => 'Citaat-sectie'],
                ],
                [
                    '_name' => 'cta',
                    '_view' => 'sections.default',
                    '_sort' => 6,
                    'active' => true,
                    'head' => ['en' => 'Call to action', 'nl' => 'Call to action'],
                    'body' => [
                        'en' => '<p>The same fields as a text section, styled to stand out. <a class="button" href="/features/sections/options">See what a card row can do</a></p>',
                        'nl' => '<p>Dezelfde velden als een tekstsectie, opvallender vormgegeven. <a class="button" href="/nl/functies/secties/opties">Bekijk wat een kaartenrij kan</a></p>',
                    ],
                ],
            ],
        ]);

        $this->page(3, [
            'title' => ['en' => 'Options', 'nl' => 'Opties'],
            'parent' => 2,
            'sort' => 1,
            'description' => [
                'en' => 'What the layout, columns, tag and period settings on a card row do — three rows of the same content, configured differently.',
                'nl' => 'Wat de instellingen voor weergave, kolommen, tag en periode op een kaartenrij doen — drie rijen met dezelfde content, anders ingesteld.',
            ],
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'One section type, four settings', 'nl' => 'Eén sectietype, vier instellingen'],
                    'body' => [
                        'en' => '<p>A card row lists a content type. Below are three of them, all listing the agenda and the releases, each set up differently. You are also two levels deep now, so the breadcrumb above has three steps and the navigation folded this page out under Sections.</p>',
                        'nl' => '<p>Een kaartenrij toont een content-type. Hieronder staan er drie, allemaal met de agenda en de releases, elk anders ingesteld. Je zit nu ook twee niveaus diep, dus het kruimelpad hierboven heeft drie stappen en de navigatie klapte deze pagina uit onder Secties.</p>',
                    ],
                ],
                [
                    '_name' => 'events',
                    '_view' => 'sections.items',
                    '_sort' => 1,
                    'active' => true,
                    'head' => ['en' => 'Period: past, in a grid of three', 'nl' => 'Periode: verleden, in een grid van drie'],
                    'period' => 'past',
                    'layout' => 'grid',
                    'columns' => 3,
                    'limit' => 3,
                ],
                [
                    '_name' => 'events',
                    '_view' => 'sections.items',
                    '_sort' => 2,
                    'active' => true,
                    'head' => ['en' => 'Fixed to one tag', 'nl' => 'Vastgezet op één tag'],
                    // Resolved by name: the ids depend on the order the tags were made
                    'tag' => Tag::where('name->en', 'Content')->value('id'),
                    'layout' => 'grid',
                    'columns' => 2,
                    'limit' => 4,
                    'link_label' => ['en' => 'The whole agenda', 'nl' => 'De hele agenda'],
                ],
                [
                    '_name' => 'news',
                    '_view' => 'sections.items',
                    '_sort' => 3,
                    'active' => true,
                    'head' => ['en' => 'A horizontal row, two in view', 'nl' => 'Een horizontale rij, twee in beeld'],
                    'layout' => 'horizontal',
                    'columns' => 2,
                    'limit' => 6,
                ],
            ],
        ]);

        // The parent the explanatory pages hang under, so the main menu stays short
        // and its fold-out is itself one of the things being demonstrated.
        $this->page(4, [
            'title' => ['en' => 'Features', 'nl' => 'Functies'],
            'sort' => 10,
            'description' => [
                'en' => 'What the panel and the template can do, each shown on the page that explains it.',
                'nl' => 'Wat het paneel en de template kunnen, elk getoond op de pagina die het uitlegt.',
            ],
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'left',
                    'head' => ['en' => 'Two pages, both live', 'nl' => 'Twee pagina\'s, allebei echt'],
                    'body' => [
                        'en' => '<p><strong>Sections</strong> renders every section type this template ships with, one after another, each explaining its own fields — and a child page shows what the options on a card row do.</p><p><strong>Multilingual</strong> is proved by the language switch in the corner, and carries a page that deliberately has no Dutch version at all.</p>',
                        'nl' => '<p><strong>Secties</strong> rendert elk sectietype dat deze template meelevert, één voor één, elk met uitleg over zijn eigen velden — en een subpagina toont wat de opties op een kaartenrij doen.</p><p><strong>Meertalig</strong> wordt bewezen door de taalschakelaar in de hoek, en heeft een pagina die met opzet helemaal geen Nederlandse versie heeft.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 1,
                    'active' => true,
                    'image_position' => 'right',
                    'head' => ['en' => 'And the rest is in the menu', 'nl' => 'En de rest staat in het menu'],
                    'body' => [
                        'en' => '<p><strong>Agenda</strong> and <strong>Releases</strong> are two of the three content archetypes, and <strong>Reference</strong> is the third — its cards are short definitions of everything named here.</p>',
                        'nl' => '<p><strong>Agenda</strong> en <strong>Releases</strong> zijn twee van de drie content-archetypes, en <strong>Naslag</strong> is de derde — die kaarten zijn korte definities van alles wat hier genoemd wordt.</p>',
                    ],
                ],
            ],
        ]);

        $this->page(5, [
            'title' => ['en' => 'Multilingual', 'nl' => 'Meertalig'],
            'parent' => 4,
            'sort' => 2,
            'description' => [
                'en' => 'Per-field translations, per-locale URLs, and a translate button that fills the rest in.',
                'nl' => 'Vertalingen per veld, URL\'s per taal, en een vertaalknop die de rest invult.',
            ],
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'left',
                    'head' => ['en' => 'The proof is the switch in the corner', 'nl' => 'Het bewijs is de schakelaar in de hoek'],
                    'body' => [
                        'en' => '<p>This site speaks English on its bare URLs and Dutch under <code>/nl</code>. The order of <code>leap.locales</code> decides which one is bare. Every page and item carries its own slug per language, so a translated page has a translated address too.</p>',
                        'nl' => '<p>Deze site spreekt Engels op de kale URL\'s en Nederlands onder <code>/nl</code>. De volgorde van <code>leap.locales</code> bepaalt welke kaal is. Elke pagina en elk item heeft een eigen slug per taal, dus een vertaalde pagina heeft ook een vertaald adres.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 1,
                    'active' => true,
                    'image_position' => 'right',
                    'head' => ['en' => 'Let AI do the second language', 'nl' => 'Laat AI de tweede taal doen'],
                    'body' => [
                        'en' => '<p>A translate button sits beside every translatable field, and one at the top fills an entire record — sections included, HTML preserved, slugs re-slugified. It is on for this demo. Try it on <a href="/features/multilingual/untranslated-example">Untranslated example</a>, the page under this one: <strong>it has no Dutch version at all</strong>. Open it in the panel, press translate, and watch it fill.</p>',
                        'nl' => '<p>Naast elk vertaalbaar veld staat een vertaalknop, en bovenaan staat er één die een heel record vult — inclusief secties, met behoud van HTML en opnieuw geslugificeerde slugs. Op deze demo staat hij aan. Probeer het op <a href="/nl/functies/meertalig">de subpagina hieronder</a>, die <strong>helemaal geen Nederlandse versie heeft</strong> — je vindt hem alleen in het Engels. Open hem in het paneel, druk op vertalen en zie hem zich vullen.</p>',
                    ],
                ],
            ],
        ]);

        // Deliberately English only: no Dutch title, no Dutch slug. It is missing from
        // the Dutch menu, has no Dutch hreflang alternate, and its 404 under /nl is the
        // router refusing to borrow another language's slug.
        $this->page(6, [
            'title' => ['en' => 'Untranslated example'],
            'slug' => ['en' => 'untranslated-example'],
            'parent' => 5,
            'sort' => 1,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'This page exists in English only'],
                    'body' => [
                        'en' => '<p>Switch to Dutch and you will not find it: it is left out of that menu, and its address returns a 404 there. A slug is an address, not prose, so it never falls back to another language — an untranslated page is simply not served in that one.</p><p>Open it in the panel and press the translate button to give it a Dutch version. Fifteen minutes after your last edit the reset empties it again.</p>',
                    ],
                ],
            ],
        ]);

        // HasSlug fills a missing translation in from the default locale's title on
        // every save, which would give this page a Dutch address after all. Clearing
        // it without events is what leaves it genuinely untranslated.
        Page::withoutEvents(function (): void {
            $page = Page::find(6);
            $page->setTranslation('slug', 'nl', '');
            $page->save();
        });

        $this->page(7, [
            'title' => ['en' => 'Get started', 'nl' => 'Aan de slag'],
            'sort' => 200,
            'description' => [
                'en' => 'Two composer requires and one artisan command, and you have this site running on your own machine.',
                'nl' => 'Twee composer-requires en één artisan-commando, en deze site draait op je eigen machine.',
            ],
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'Install', 'nl' => 'Installeren'],
                    'body' => [
                        'en' => '<p>The panel is a runtime dependency; the scaffolding is not.</p><pre><code>composer require nickdekruijk/leap
composer require --dev nickdekruijk/leap-template
php artisan leap:template</code></pre><p>The installer asks which languages you want and which content types to generate, then runs the migrations and creates your first user.</p>',
                        'nl' => '<p>Het paneel is een runtime-dependency; de scaffolding niet.</p><pre><code>composer require nickdekruijk/leap
composer require --dev nickdekruijk/leap-template
php artisan leap:template</code></pre><p>De installer vraagt welke talen je wilt en welke content-types hij moet genereren, draait daarna de migraties en maakt je eerste gebruiker aan.</p>',
                    ],
                ],
                [
                    '_name' => 'default',
                    '_sort' => 1,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'Turn the AI features on', 'nl' => 'Zet de AI-functies aan'],
                    'body' => [
                        'en' => '<p>Every AI task is off until you name a provider in <code>config/leap.php</code>, and each one picks its own — so you can send translations to a translation engine and images to an image model:</p><pre><code>\'translate\' => [\'provider\' => \'deepl\'],
\'alt_text\'  => [\'provider\' => \'claude\'],
\'image\'     => [\'provider\' => \'gemini\'],</code></pre><p>Add the matching key to your <code>.env</code> and the buttons appear; a task whose provider has no key stays hidden. On this demo translation and alt texts are on and image generation is off, because a click on it costs real money and every visitor here shares one account.</p>',
                        'nl' => '<p>Elke AI-taak staat uit tot je een provider noemt in <code>config/leap.php</code>, en elke taak kiest zijn eigen — je kunt vertalingen dus naar een vertaalmachine sturen en beelden naar een beeldmodel:</p><pre><code>\'translate\' => [\'provider\' => \'deepl\'],
\'alt_text\'  => [\'provider\' => \'claude\'],
\'image\'     => [\'provider\' => \'gemini\'],</code></pre><p>Zet de bijbehorende sleutel in je <code>.env</code> en de knoppen verschijnen; een taak waarvan de provider geen sleutel heeft blijft verborgen. Op deze demo staan vertalen en alt-teksten aan en beeldgeneratie uit, omdat een klik daarop echt geld kost en elke bezoeker hier één account deelt.</p>',
                    ],
                ],
                [
                    '_name' => 'cta',
                    '_view' => 'sections.default',
                    '_sort' => 2,
                    'active' => true,
                    'head' => ['en' => 'Read the documentation', 'nl' => 'Lees de documentatie'],
                    'body' => [
                        'en' => '<p><a class="button" href="https://github.com/nickdekruijk/leap">nickdekruijk/leap on GitHub</a></p>',
                        'nl' => '<p><a class="button" href="https://github.com/nickdekruijk/leap">nickdekruijk/leap op GitHub</a></p>',
                    ],
                ],
            ],
        ]);

        $this->page(8, [
            'title' => ['en' => 'Privacy', 'nl' => 'Privacy'],
            'menuitem' => false,
            'sort' => 300,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'Privacy', 'nl' => 'Privacy'],
                    'body' => [
                        'en' => '<p>This is a demo. Anything you type into it is public, and the database is rebuilt from scratch a quarter of an hour after the last change, so nothing you enter is kept.</p>',
                        'nl' => '<p>Dit is een demo. Alles wat je hier invult is openbaar, en de database wordt een kwartier na de laatste wijziging volledig opnieuw opgebouwd, dus niets wat je invoert blijft bewaard.</p>',
                    ],
                ],
                [
                    '_name' => 'cookies',
                    '_sort' => 1,
                    'active' => true,
                    'head' => ['en' => 'Cookies', 'nl' => 'Cookies'],
                    'body' => [
                        'en' => '<p>The table below is rendered from the same configuration the consent banner enforces, so this page cannot drift away from what the site actually does.</p>',
                        'nl' => '<p>De tabel hieronder wordt gerenderd uit dezelfde configuratie die de toestemmingsbalk afdwingt, dus deze pagina kan niet uit de pas gaan lopen met wat de site echt doet.</p>',
                    ],
                ],
            ],
        ]);

        $this->page(9, [
            'title' => ['en' => 'Terms', 'nl' => 'Voorwaarden'],
            'menuitem' => false,
            'sort' => 310,
            'sections' => [
                [
                    '_name' => 'default',
                    '_sort' => 0,
                    'active' => true,
                    'image_position' => 'none',
                    'head' => ['en' => 'Terms', 'nl' => 'Voorwaarden'],
                    'body' => [
                        'en' => '<p>Leap and its template are open source under the MIT licence. This demo comes with no guarantees at all — it is here to be poked at.</p>',
                        'nl' => '<p>Leap en de template zijn open source onder de MIT-licentie. Deze demo komt met geen enkele garantie — hij staat er om aan te zitten.</p>',
                    ],
                ],
            ],
        ]);
    }

    /**
     * The images on the pages, keyed by the array position of the section they belong
     * to. Sections gets a background rather than an inline image, and a second and
     * third file in one field to make the gallery.
     */
    protected function seedPageImages(): void
    {
        $this->attachSectionImages(Page::find(1), [
            0 => 'slides/01.jpg',
            1 => 'slides/02.jpg',
            2 => 'slides/03.jpg',
            3 => 'abstract/03.jpg',
            4 => 'abstract/04.jpg',
            5 => 'wide/01.jpg',
        ]);

        $this->attachImage(Page::find(2), 'wide/02.jpg', 'sections.2.background', ['en' => 'Background image', 'nl' => 'Achtergrondafbeelding']);
        $this->attachSectionImages(Page::find(2), [3 => 'abstract/05.jpg']);
        $this->attachImage(Page::find(2), 'abstract/06.jpg', 'sections.3.image', ['en' => 'Second image of the gallery', 'nl' => 'Tweede afbeelding van de galerij'], 1);
        $this->attachImage(Page::find(2), 'abstract/07.jpg', 'sections.3.image', ['en' => 'Third image of the gallery', 'nl' => 'Derde afbeelding van de galerij'], 2);

        $this->attachSectionImages(Page::find(4), [0 => 'abstract/01.jpg', 1 => 'abstract/02.jpg']);
        $this->attachSectionImages(Page::find(5), [0 => 'abstract/08.jpg', 1 => 'abstract/05.jpg']);

        // One focus point, so the object-position of a cropped card differs from the
        // centre the others fall back to
        if ($media = Media::findFile('abstract/01.jpg')) {
            $media->meta = ['image_focus' => ['x' => 25, 'y' => 30]] + ($media->meta ?? []);
            $media->save();
        }
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
                '_sort' => 8 + $index,
                'active' => true,
                'head' => $overview['title'],
                'limit' => 6,
                ...$overview['teaser'] ?? [],
            ])->all();
    }

    /**
     * One page per content type. An unlimited, untagged items section is what makes a
     * page the canonical overview and gives every item of that type its detail URL.
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
                'sections' => [
                    ...$overview['intro'] ?? [],
                    [
                        '_name' => $overview['key'],
                        '_view' => 'sections.items',
                        '_sort' => 1,
                        'active' => true,
                        'head' => $overview['title'],
                        ...$overview['overview'] ?? [],
                    ],
                ],
            ]);
        }
    }

    /**
     * The packages whose changelogs fill the releases, and what a release of each is
     * called on the card. Read from the copy in vendor/ when it is there, so the demo
     * shows the version it actually runs; the template is a dev dependency and is
     * missing on a host that installs with --no-dev, and is then fetched from GitHub.
     */
    protected const CHANGELOGS = [
        'Leap' => 'nickdekruijk/leap',
        'Template' => 'nickdekruijk/leap-template',
    ];

    /**
     * The releases of both packages, newest first. When neither changelog can be read
     * the hand-written items in demo-content.php take over, because an unattended
     * reset must never fail over a parse.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function releasesFromChangelog(int $perPackage = 5): array
    {
        $items = [];

        foreach (array_keys(self::CHANGELOGS) as $offset => $package) {
            $items = array_merge($items, $this->releasesOf($package, $perPackage, $offset));
        }

        usort($items, fn (array $a, array $b): int => strcmp($b['published'], $a['published']));

        return $items;
    }

    /**
     * One package's changelog, parsed into cards.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function releasesOf(string $package, int $limit, int $offset): array
    {
        if (! $markdown = $this->changelogOf(self::CHANGELOGS[$package])) {
            return [];
        }

        preg_match_all(
            '/^## \[([^\]]+)\](?: — (\d{4}-\d{2}-\d{2}))?\R(.*?)(?=^## \[|\z)/ms',
            $markdown,
            $releases,
            PREG_SET_ORDER,
        );

        $items = [];
        $perDay = [];

        foreach (array_slice($releases, 0, $limit) as $index => [, $version, $date, $body]) {
            // Every entry opens with a bold sentence saying what changed
            preg_match_all('/^- \*\*(.+?)\*\*(.*)$/m', $body, $highlights, PREG_SET_ORDER);

            if ($highlights === []) {
                continue;
            }

            $unreleased = ! $date;
            // Two releases can share a day, and the file is newest first: stepping the
            // clock back per entry keeps that order after the database sorts on it.
            // The extra minute per package keeps the two apart on a shared date.
            $seen = $perDay[$date] = ($perDay[$date] ?? -1) + 1;
            $published = $unreleased
                ? now()->addDays(10)->subMinutes($offset)->toDateTimeString()
                : Carbon::parse($date)->setTime(12, 0)->subMinutes($seen * 30 + $offset)->toDateTimeString();

            // Title and intro are plain text on the card, so they get the markdown
            // stripped rather than turned into HTML — escaping them here would show
            // the entities, since Blade escapes them again on the way out
            $headline = $this->plainMarkdown($highlights[0][1]);

            // The headline is the intro: it is the one sentence saying what the release
            // did, and it reads better as the card's text than crammed into its title
            $intro = $unreleased
                ? 'Not out yet: this entry has no release date, so the demo dates it in the future — invisible on the site and waiting in the panel.'
                : Str::limit($headline, 180);

            // A release with a single change says it once, in prose; one with several
            // lists them. Named $html, not $body: $body still holds the markdown that
            // the section headings are read from below.
            $html = count($highlights) > 1
                ? '<ul><li>'.collect($highlights)->map(fn (array $h): string => $this->inlineMarkdown($h[1]))->implode('</li><li>').'</li></ul>'
                : '<p>'.$this->inlineMarkdown($highlights[0][1]).' '.$this->inlineMarkdown(trim($highlights[0][2])).'</p>';

            // Keep a Changelog groups an entry under Added, Fixed, Changed and the
            // like — exactly the filter a changelog wants, and already in the file
            preg_match_all('/^### (.+)$/m', $body, $sections);

            $items[] = [
                'image' => 'abstract/0'.(count($items) % 8 + 1).'.jpg',
                // Spelled out rather than left to Str::slug, which drops the dots: it
                // would give 1.1.10 and 1.11.0 the same "1110" and leave HasSlug to
                // tell them apart with a -2 nobody can read
                'slug' => $this->bothLocales(Str::slug($package.' '.str_replace('.', ' ', $unreleased ? 'unreleased' : $version))),
                'tags' => array_merge([$package], array_map(trim(...), $sections[1])),
                'published' => $published,
                'title' => $this->bothLocales($package.' '.($unreleased ? 'unreleased' : $version)),
                'intro' => $this->introWithLanguageNote($intro),
                'head' => $this->bothLocales('In this release'),
                'body' => $this->bothLocales(
                    $html.'<p><a href="https://github.com/nickdekruijk/'.($package === 'Template' ? 'leap-template' : 'leap').'/blob/master/CHANGELOG.md">The full entry in the changelog</a></p>'
                ),
            ];
        }

        return $items;
    }

    /**
     * A package's changelog: the installed copy when there is one, otherwise the one
     * on GitHub. The demo host installs without dev dependencies, so the template's
     * copy is not on disk there — and reading it over HTTP beats leaving half the
     * releases out.
     *
     * The reset runs unattended, so a slow or unreachable GitHub must not hold it up:
     * a short timeout, and any failure at all simply means no releases from that
     * package this time.
     */
    protected function changelogOf(string $package): ?string
    {
        if (File::exists($file = base_path('vendor/'.$package.'/CHANGELOG.md'))) {
            return File::get($file);
        }

        try {
            $response = Http::timeout(5)->get('https://raw.githubusercontent.com/'.$package.'/master/CHANGELOG.md');

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * The card text, marked in every locale that is not the site's default. A Dutch
     * reader is looking at English prose either way; saying so in front of it costs a
     * word and saves the wondering. The title stays clean, and the overview above the
     * cards explains why once, at length.
     *
     * @return array<string, string>
     */
    protected function introWithLanguageNote(string $intro): array
    {
        $default = array_key_first(config('leap.locales') ?: ['en' => 'English']);

        return collect(array_keys(config('leap.locales') ?: ['en' => 'English']))
            ->mapWithKeys(fn (string $locale): array => [
                $locale => $locale === $default ? $intro : '['.__('English only', [], $locale).'] '.$intro,
            ])->all();
    }

    /**
     * The changelog is written in English and release notes are not translated, so
     * both locales get the same text rather than a machine-made Dutch one.
     *
     * @return array<string, string>
     */
    protected function bothLocales(string $text): array
    {
        return collect(array_keys(config('leap.locales') ?: ['en' => 'English']))
            ->mapWithKeys(fn (string $locale): array => [$locale => $text])
            ->all();
    }

    /**
     * The same headline without any markup at all, for the places that are plain text.
     */
    protected function plainMarkdown(string $text): string
    {
        $text = preg_replace('/`([^`]+)`/', '$1', trim($text));

        return trim(preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '$1', $text));
    }

    /**
     * Just enough markdown for a changelog headline: code spans and links.
     */
    protected function inlineMarkdown(string $text): string
    {
        $text = e(trim($text));
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

        return preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text);
    }

    protected function seedNews(): void
    {
        if (News::count() > 0) {
            return;
        }

        foreach ($this->releasesFromChangelog() ?: $this->content['news'] as $index => $item) {
            $news = News::create($this->item($item) + array_filter([
                // Released versions keep their real date; the one that is not out yet
                // is dated relative to now, so it stays in the future after every reset
                'published_at' => Carbon::parse($item['published']),
                'slug' => $item['slug'] ?? null,
            ]));

            $this->attachTags($news, $item['tags'] ?? []);
            $this->attachImage($news, $item['image'] ?? null, 'images', $item['title']);
        }
    }

    protected function seedEvents(): void
    {
        if (Event::count() > 0) {
            return;
        }

        // The times are the point of these items: all day, a start without an end, and
        // an end before its start — the three cases the model derives ends_at from.
        $upcoming = [
            ['weeks' => 1, 'start_time' => '13:30', 'end_time' => '17:00'],
            ['weeks' => 2, 'start_time' => null, 'end_time' => null],
            ['weeks' => 3, 'start_time' => '17:00', 'end_time' => null],
            ['weeks' => 4, 'start_time' => '20:00', 'end_time' => '02:30'],
            ['weeks' => 5, 'start_time' => '10:00', 'end_time' => '16:00'],
            ['weeks' => 6, 'start_time' => '19:30', 'end_time' => '21:00'],
        ];

        foreach ($this->content['events']['future'] as $index => $item) {
            $event = Event::create($this->item($item) + [
                'date' => now()->addWeeks($upcoming[$index]['weeks'])->startOfDay(),
                'start_time' => $upcoming[$index]['start_time'],
                'end_time' => $upcoming[$index]['end_time'],
            ]);

            // The fifth item is the one the tag-fixed row on Sections › Options points
            // at, so it gets that exact tag rather than the rotation's
            $this->attachTags($event, $item['tags'] ?? []);

            $this->attachImage($event, $item['image'] ?? null, 'images', $item['title']);
        }

        $finished = [
            ['weeks' => 3, 'start_time' => '19:00', 'end_time' => '21:00'],
            ['weeks' => 6, 'start_time' => '14:00', 'end_time' => '17:00'],
            ['weeks' => 13, 'start_time' => null, 'end_time' => null],
            ['weeks' => 17, 'start_time' => '20:00', 'end_time' => '01:30'],
            ['weeks' => 26, 'start_time' => '10:00', 'end_time' => '12:00'],
            ['weeks' => 48, 'start_time' => '09:30', 'end_time' => '17:00'],
        ];

        foreach ($this->content['events']['past'] as $index => $item) {
            $event = Event::create($this->item($item) + [
                'date' => now()->subWeeks($finished[$index]['weeks'])->startOfDay(),
                'start_time' => $finished[$index]['start_time'],
                'end_time' => $finished[$index]['end_time'],
            ]);

            $this->attachTags($event, $item['tags'] ?? []);
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

            $this->attachTags($project, $item['tags'] ?? []);
            $this->attachImage($project, $item['image'] ?? null, 'images', $item['title']);
        }
    }

    /**
     * The columns every content type shares: what a visitor reads on the card and on
     * the detail page.
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
     * Put a generated image on the public disk and hang it off a model, the way the
     * editor's own syncMedia() does: nothing goes into the sections JSON, the
     * attachment is a leap_mediables row naming the attribute it belongs to
     * ('images' for a whole item, 'sections.{key}.image' for one section of a page).
     *
     * A missing source file is not an error. The images are generated once by
     * demo:images and committed, but a clone without them still has to seed a working
     * site — it simply gets the placeholder gradients instead.
     *
     * @param  array<string, string>  $alt  Alt text per locale
     */
    protected function attachImage(Model $model, ?string $file, string $attribute, array $alt = [], int $sort = 0): void
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
            ->where('sort', $sort)
            ->delete();

        Mediable::create([
            'media_id' => $media->id,
            'mediable_type' => $model::class,
            'mediable_id' => $model->id,
            'mediable_attribute' => $attribute,
            'sort' => $sort,
        ]);
    }

    /**
     * The images on a page, keyed by the array position of the section they belong to
     * — the key in the sections JSON, not its _sort.
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
     * The tags an item names, by their English label. A tag the vocabulary does not
     * have yet is created on the spot — that is how the changelog's own Added, Fixed
     * and Changed headings become chips above the releases.
     *
     * @param  array<int, string>  $names
     */
    protected function attachTags(News|Event|Project $item, array $names): void
    {
        $ids = collect($names)->map(function (string $name): int {
            $labels = self::TAGS[$name] ?? ['en' => $name, 'nl' => $name];

            return Tag::firstOrCreate(
                ['name->en' => $labels['en']],
                ['name' => $labels, 'sort' => Tag::max('sort') + 1],
            )->id;
        })->all();

        $item->tags()->sync($ids);
    }

    /**
     * Footer settings, when the settings package is installed. The two script fields
     * carry a commented-out snippet so the consent parking mechanism has something to
     * park: the template renders them inside a <template data-consent> that only runs
     * once the visitor allows that category.
     */
    protected function seedSettings(): void
    {
        if (! class_exists(Setting::class)) {
            return;
        }

        Setting::set([
            'html_head' => [
                'value' => '',
                'description' => 'Code right before </head>. Only for code that needs no consent — trackers belong in the scripts_ settings, or they run outside the consent system.',
            ],
            'scripts_analytics' => [
                'value' => "<!-- Paste your analytics snippet here. It is parked in a <template>\n     until the visitor allows the statistics category. -->",
                'description' => 'Statistics scripts (e.g. Google Analytics). Runs only after the visitor allows statistics.',
            ],
            'scripts_embeds' => [
                'value' => "<!-- Snippets for embedded content go here, parked the same way\n     until the visitor allows embedded content. -->",
                'description' => 'Scripts for embedded content. Runs only after the visitor allows it.',
            ],
            'footer_contact' => [
                'value' => "Leap demo\nA starting point, not a finished site\ninfo@example.com",
                'description' => 'Address/contact details in the footer',
            ],
            'socials' => [
                'value' => 'github:https://github.com/nickdekruijk/leap',
                'description' => 'Social media, one "name:url" per line (name = FontAwesome brand icon)',
            ],
            'footer_copyright' => [
                'value' => '© '.date('Y').' '.config('app.name').' — MIT licensed, rebuilt every 15 minutes',
                'description' => 'Copyright line at the bottom of the footer',
            ],
            'footer_links' => [
                'value' => "Privacy:/privacy\nTerms:/terms",
                'description' => 'Footer links, one "label:url" per line',
            ],
            'og_image' => [
                'value' => '/storage/slides/01.jpg',
                'description' => 'Default social share image (URL or /storage path); a page\'s own image wins',
            ],
        ]);
    }
}
