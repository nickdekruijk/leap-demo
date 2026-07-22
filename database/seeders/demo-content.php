<?php

/**
 * The demo's content items, in English and Dutch. The demo documents itself: every
 * item says what it demonstrates and where to change it in the admin panel, because
 * this site is a starting point for your own project rather than a finished website.
 *
 * Keys stay news/events/projects — those are the models — while the overview pages
 * they hang under are titled Releases, Agenda and Building blocks.
 *
 * News carries a `published` date: an absolute one for a released version, a relative
 * one for the version that is not out yet. Two releases can share a day, so the times
 * are part of it — without them the order between them is whatever the database
 * happens to return.
 */
return [

    // ── Releases ─────────────────────────────────────────────────────────────
    // The real Leap changelog. The first one is dated in the future on purpose.
    'news' => [
        [
            'image' => 'abstract/01.jpg',
            'published' => '+10 days',
            'title' => ['en' => '0.11.0 — not published yet', 'nl' => '0.11.0 — nog niet gepubliceerd'],
            'intro' => [
                'en' => 'You are not seeing this on the website: its publication date is in the future. Log in and it is right there in the admin, waiting.',
                'nl' => 'Op de website zie je dit niet: de publicatiedatum ligt in de toekomst. Log in en het staat gewoon in het paneel te wachten.',
            ],
            'head' => ['en' => 'Write now, publish later', 'nl' => 'Nu schrijven, later publiceren'],
            'body' => [
                'en' => '<p>The news archetype treats <code>published_at</code> as a gate: an item dated in the future is invisible to visitors and stays editable in the panel until that moment passes. No queue, no cron — the query simply does not return it yet.</p><p>Open <em>Releases</em> in the admin to find this item, then move its date back to today and watch it appear on the site.</p>',
                'nl' => '<p>Het nieuws-archetype gebruikt <code>published_at</code> als poort: een item met een datum in de toekomst is onzichtbaar voor bezoekers en blijft in het paneel bewerkbaar tot dat moment voorbij is. Geen queue, geen cron — de query geeft het simpelweg nog niet terug.</p><p>Open <em>Releases</em> in het paneel om dit item te vinden, zet de datum op vandaag en zie het op de site verschijnen.</p>',
            ],
        ],
        [
            'image' => 'abstract/02.jpg',
            'published' => '2026-07-21 11:20',
            'title' => ['en' => '0.10.15 — a switched-off section keeps the carousel closed', 'nl' => '0.10.15 — een uitgezette sectie houdt de carrousel dicht'],
            'intro' => [
                'en' => 'A carousel is a run of slide sections: the first opens the wrapper, the last closes it. Deactivating the last slide used to take the closing tag with it.',
                'nl' => 'Een carrousel is een reeks slide-secties: de eerste opent de wrapper, de laatste sluit hem. Het uitzetten van de laatste slide nam dat sluittag mee.',
            ],
            'head' => ['en' => 'What changed', 'nl' => 'Wat er veranderde'],
            'body' => [
                'en' => '<p><code>sections()</code> now drops inactive sections itself, before the run is marked, so a template can no longer get the order wrong. Templates that filtered on <code>active</code> themselves keep working; the filter is simply redundant now.</p>',
                'nl' => '<p><code>sections()</code> gooit inactieve secties nu zelf weg, vóórdat de reeks gemarkeerd wordt, zodat een template de volgorde niet meer verkeerd kan krijgen. Templates die zelf op <code>active</code> filterden blijven werken; dat filter is nu alleen overbodig.</p>',
            ],
        ],
        [
            'image' => 'abstract/03.jpg',
            'published' => '2026-07-20 16:45',
            'title' => ['en' => '0.10.14 — showIf() reads its trigger in the right language', 'nl' => '0.10.14 — showIf() leest zijn trigger in de juiste taal'],
            'intro' => [
                'en' => 'A translatable field is stored per locale, and in JavaScript an object is always truthy — so a conditional field appeared the moment its trigger was touched in any language.',
                'nl' => 'Een vertaalbaar veld wordt per taal opgeslagen, en in JavaScript is een object altijd waar — dus een voorwaardelijk veld verscheen zodra de trigger in wélke taal dan ook was aangeraakt.',
            ],
            'head' => ['en' => 'Also in this release', 'nl' => 'Ook in deze release'],
            'body' => [
                'en' => '<p>A hidden field no longer leaves a gap where its row was, and <code>showWhenTrue()</code> was renamed to <code>showIf()</code> — the old name promised a boolean while any truthy value has always shown the field. The old name stays as a deprecated alias.</p>',
                'nl' => '<p>Een verborgen veld laat geen gat meer achter waar zijn rij stond, en <code>showWhenTrue()</code> heet nu <code>showIf()</code> — de oude naam beloofde een boolean terwijl elke waarheidsachtige waarde het veld altijd al toonde. De oude naam blijft als verouderd alias bestaan.</p>',
            ],
        ],
        [
            'image' => 'abstract/04.jpg',
            'published' => '2026-07-20 09:30',
            'title' => ['en' => '0.10.13 — leap:user --role attaches a role without asking', 'nl' => '0.10.13 — leap:user --role koppelt een rol zonder te vragen'],
            'intro' => [
                'en' => 'A user without a role sees nothing in the panel, and the only way to fix that was a question a scripted install has nobody to answer.',
                'nl' => 'Een gebruiker zonder rol ziet niets in het paneel, en dat was alleen te verhelpen met een vraag die een geautomatiseerde installatie aan niemand kan stellen.',
            ],
            'head' => ['en' => 'How it works', 'nl' => 'Hoe het werkt'],
            'body' => [
                'en' => '<p>Bare <code>--role</code> takes the first role, <code>--role=superuser</code> or <code>--role=1</code> names one, and an unknown name fails with a message instead of leaving an account that cannot log into anything.</p>',
                'nl' => '<p>Kaal <code>--role</code> pakt de eerste rol, <code>--role=superuser</code> of <code>--role=1</code> wijst er één aan, en een onbekende naam faalt met een melding in plaats van een account achter te laten dat nergens in kan.</p>',
            ],
        ],
        [
            'image' => 'abstract/05.jpg',
            'published' => '2026-07-17 14:10',
            'title' => ['en' => '0.10.12 — an index filters by id, not by rendered text', 'nl' => '0.10.12 — een index filtert op id, niet op gerenderde tekst'],
            'intro' => [
                'en' => 'A pivot column renders as its values joined into one string, and the filter was built from that string — so an article with two tags offered the pair as a filter option of its own.',
                'nl' => 'Een pivot-kolom rendert als zijn waarden aan elkaar geplakt, en het filter werd uit die tekst opgebouwd — dus een artikel met twee tags bood dat paar aan als eigen filteroptie.',
            ],
            'head' => ['en' => 'Four fixes of the same shape', 'nl' => 'Vier fixes van dezelfde vorm'],
            'body' => [
                'en' => '<p>All four came from asking the database for a translatable attribute by column name and getting the raw json back: searching matched the locale keys, <code>unique</code> validated the json, and ordering sorted by it. Each now speaks to the text inside instead.</p>',
                'nl' => '<p>Alle vier kwamen voort uit het opvragen van een vertaalbaar attribuut op kolomnaam, waarbij de ruwe json terugkwam: zoeken matchte op de taalsleutels, <code>unique</code> valideerde de json, en sorteren ordende erop. Elk kijkt nu naar de tekst erin.</p>',
            ],
        ],
        [
            'image' => 'abstract/06.jpg',
            'published' => '2026-07-16 10:05',
            'title' => ['en' => '0.10.11 — index grouping only when it makes sense', 'nl' => '0.10.11 — index-groepering alleen wanneer het klopt'],
            'intro' => [
                'en' => 'The group header is the first letter of the sorted column, which says nothing at all when that column is a date or a number.',
                'nl' => 'De groepskop is de eerste letter van de gesorteerde kolom, en die zegt helemaal niets wanneer die kolom een datum of getal is.',
            ],
            'head' => ['en' => 'Older releases', 'nl' => 'Oudere releases'],
            'body' => [
                'en' => '<p>The full history lives in the <a href="https://github.com/nickdekruijk/leap/blob/master/CHANGELOG.md">changelog on GitHub</a>. These six items exist to show what the news archetype does with dates — replace them with your own.</p>',
                'nl' => '<p>De volledige geschiedenis staat in de <a href="https://github.com/nickdekruijk/leap/blob/master/CHANGELOG.md">changelog op GitHub</a>. Deze zes items bestaan om te tonen wat het nieuws-archetype met datums doet — vervang ze door je eigen berichten.</p>',
            ],
        ],
    ],

    // ── Agenda ───────────────────────────────────────────────────────────────
    // Every item names the date or time case it demonstrates.
    'events' => [

        'future' => [
            [
                'image' => 'abstract/07.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'An ordinary event, with a start and an end', 'nl' => 'Een gewoon event, met begin- en eindtijd'],
                'intro' => [
                    'en' => 'The everyday case: one date, two times. The card shows them side by side and the detail page writes the weekday out in full.',
                    'nl' => 'Het alledaagse geval: één datum, twee tijden. De kaart toont ze naast elkaar en de detailpagina schrijft de weekdag voluit.',
                ],
                'head' => ['en' => 'What to look at', 'nl' => 'Waar je op moet letten'],
                'body' => [
                    'en' => '<p>Open this item in the admin and you will find a date field and two time fields, nothing more. Everything else — the ordering, the upcoming filter, the moment it drops off the list — is derived from them.</p>',
                    'nl' => '<p>Open dit item in het paneel en je vindt een datumveld en twee tijdvelden, meer niet. Al het andere — de volgorde, het aankomend-filter, het moment waarop het van de lijst valt — wordt daaruit afgeleid.</p>',
                ],
            ],
            [
                'image' => 'abstract/08.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'All day: no times at all', 'nl' => 'Hele dag: helemaal geen tijden'],
                'intro' => [
                    'en' => 'Leave both time fields empty and the event lasts the whole day. It stays listed as upcoming until that day is over.',
                    'nl' => 'Laat beide tijdvelden leeg en het event duurt de hele dag. Het blijft als aankomend in de lijst tot die dag voorbij is.',
                ],
                'head' => ['en' => 'Derived, not stored', 'nl' => 'Afgeleid, niet opgeslagen'],
                'body' => [
                    'en' => '<p>The model keeps an <code>ends_at</code> column in step with the date and times on every save, so the overview can ask one simple question: is this over yet?</p>',
                    'nl' => '<p>Het model houdt bij elke opslag een <code>ends_at</code>-kolom in de pas met de datum en tijden, zodat het overzicht één simpele vraag kan stellen: is dit al voorbij?</p>',
                ],
            ],
            [
                'image' => 'abstract/01.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'A start time only — it runs until the end of the day', 'nl' => 'Alleen een begintijd — het loopt tot het eind van de dag'],
                'intro' => [
                    'en' => 'An opening, a drink, anything nobody commits an end to. The card shows the start on its own, without a dash trailing off into nothing.',
                    'nl' => 'Een opening, een borrel, alles waar niemand een eindtijd aan vastlegt. De kaart toont alleen de begintijd, zonder streepje dat nergens op uitkomt.',
                ],
                'head' => ['en' => 'Why it matters', 'nl' => 'Waarom dat uitmaakt'],
                'body' => [
                    'en' => '<p>Without an end time the event is treated as running until midnight, so it does not disappear from the overview halfway through its own afternoon.</p>',
                    'nl' => '<p>Zonder eindtijd geldt het event als lopend tot middernacht, zodat het niet halverwege zijn eigen middag uit het overzicht verdwijnt.</p>',
                ],
            ],
            [
                'image' => 'abstract/02.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'An end before its start — it finishes the next day', 'nl' => 'Een eind vóór het begin — het loopt door tot de volgende dag'],
                'intro' => [
                    'en' => 'Eight in the evening until half past two. The model reads an end at or before the start as running past midnight.',
                    'nl' => 'Van acht uur \'s avonds tot half drie. Het model leest een eind op of vóór het begin als doorlopend over middernacht.',
                ],
                'head' => ['en' => 'One rule, no extra field', 'nl' => 'Eén regel, geen extra veld'],
                'body' => [
                    'en' => '<p>There is no "ends next day" switch to forget: the times say it themselves. This is the kind of edge case a starter template is worth having already solved.</p>',
                    'nl' => '<p>Er is geen "eindigt volgende dag"-schakelaar om te vergeten: de tijden zeggen het zelf. Precies het soort randgeval waarvan het prettig is dat een starttemplate het al heeft opgelost.</p>',
                ],
            ],
            [
                'image' => 'abstract/03.jpg',
                'tags' => ['Content'],
                'title' => ['en' => 'Tagged, so a fixed row can pick it out', 'nl' => 'Getagd, zodat een vaste rij het eruit kan pikken'],
                'intro' => [
                    'en' => 'A card row can be narrowed to a single tag. This item carries the tag the row on Sections › Options is fixed to.',
                    'nl' => 'Een kaartenrij kan tot één tag beperkt worden. Dit item draagt de tag waarop de rij op Secties › Opties is vastgezet.',
                ],
                'head' => ['en' => 'Where to set it', 'nl' => 'Waar je dat instelt'],
                'body' => [
                    'en' => '<p>In the page editor, every card row has a <em>tag</em> select next to its layout and column count. Leave it empty and the row shows everything.</p>',
                    'nl' => '<p>In de pagina-editor heeft elke kaartenrij een <em>tag</em>-keuze naast de weergave en het aantal kolommen. Laat hem leeg en de rij toont alles.</p>',
                ],
            ],
            [
                'image' => 'abstract/04.jpg',
                'tags' => ['Media'],
                'title' => ['en' => 'Two events share one photograph', 'nl' => 'Twee events delen één afbeelding'],
                'intro' => [
                    'en' => 'Media is stored once and attached wherever it is needed, so the same file can sit on a dozen records without a dozen copies.',
                    'nl' => 'Media wordt één keer opgeslagen en gekoppeld waar het nodig is, dus hetzelfde bestand kan op een dozijn records zitten zonder een dozijn kopieën.',
                ],
                'head' => ['en' => 'The file manager', 'nl' => 'De bestandsbeheerder'],
                'body' => [
                    'en' => '<p>Upload once, then pick the same file again from any media field. Crop it, set a focus point or let AI write its alt text, and every record showing it follows along.</p>',
                    'nl' => '<p>Upload één keer en kies daarna hetzelfde bestand bij elk mediaveld. Snijd het bij, zet een focuspunt of laat AI de alt-tekst schrijven, en elk record dat het toont gaat mee.</p>',
                ],
            ],
        ],

        'past' => [
            [
                'image' => 'abstract/05.jpg',
                'tags' => ['Content'],
                'title' => ['en' => 'Past events are hidden by default', 'nl' => 'Afgelopen events zijn standaard verborgen'],
                'intro' => [
                    'en' => 'An agenda answers "what is coming", so a card row of an event type shows upcoming items and nothing else unless you say otherwise.',
                    'nl' => 'Een agenda beantwoordt "wat komt eraan", dus een kaartenrij van een event-type toont alleen aankomende items, tenzij je iets anders instelt.',
                ],
                'head' => ['en' => 'You are looking at one now', 'nl' => 'Je kijkt er nu naar één'],
                'body' => [
                    'en' => '<p>The row that brought you here has its <em>period</em> set to past, which is the only reason these six items are visible at all. Set it back to upcoming and this whole list disappears.</p>',
                    'nl' => '<p>De rij die je hier bracht heeft <em>periode</em> op verleden staan, en alleen daarom zijn deze zes items zichtbaar. Zet hem terug op aankomend en de hele lijst verdwijnt.</p>',
                ],
            ],
            [
                'image' => 'abstract/06.jpg',
                'tags' => ['Content'],
                'title' => ['en' => 'Or show both, upcoming first', 'nl' => 'Of toon beide, aankomend eerst'],
                'intro' => [
                    'en' => 'The third setting lists what is coming and then what has been, in one row, which suits an archive page better than two separate lists.',
                    'nl' => 'De derde instelling toont eerst wat komt en dan wat is geweest, in één rij — dat past beter bij een archiefpagina dan twee losse lijsten.',
                ],
                'head' => ['en' => 'Three values', 'nl' => 'Drie waarden'],
                'body' => [
                    'en' => '<p>Upcoming, past, both. The select only appears on rows of a content type that has dates, because it would mean nothing on the others.</p>',
                    'nl' => '<p>Aankomend, verleden, beide. De keuze verschijnt alleen bij rijen van een content-type met datums, omdat hij bij de andere niets zou betekenen.</p>',
                ],
            ],
            [
                'image' => 'abstract/07.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'A past all-day event', 'nl' => 'Een afgelopen event van een hele dag'],
                'intro' => [
                    'en' => 'The same rules run backwards: no times means the whole day, and the day is over, so here it is in the past list.',
                    'nl' => 'Dezelfde regels werken terug: geen tijden betekent de hele dag, en die dag is voorbij, dus staat het hier in de verleden-lijst.',
                ],
                'head' => ['en' => 'Ordering', 'nl' => 'Volgorde'],
                'body' => [
                    'en' => '<p>Past items are listed most recent first, which is almost always what an archive wants and never what an agenda wants.</p>',
                    'nl' => '<p>Afgelopen items staan met de meest recente eerst, wat een archief vrijwel altijd wil en een agenda nooit.</p>',
                ],
            ],
            [
                'image' => 'abstract/08.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'An evening that ran past midnight', 'nl' => 'Een avond die over middernacht liep'],
                'intro' => [
                    'en' => 'The same next-day rule as its upcoming counterpart, seen from the other side of the date.',
                    'nl' => 'Dezelfde volgende-dag-regel als zijn aankomende tegenhanger, gezien vanaf de andere kant van de datum.',
                ],
                'head' => ['en' => 'Still one date', 'nl' => 'Nog steeds één datum'],
                'body' => [
                    'en' => '<p>An event has one date. Everything about how long it lasts is in the times, which keeps the editor simple and the queries honest.</p>',
                    'nl' => '<p>Een event heeft één datum. Alles over hoe lang het duurt zit in de tijden, wat de editor eenvoudig houdt en de queries eerlijk.</p>',
                ],
            ],
            [
                'image' => 'abstract/01.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'Half a year ago', 'nl' => 'Een half jaar geleden'],
                'intro' => [
                    'en' => 'The set is spread across the year so the past list has something to sort, and so the date formatting can be seen in more than one month.',
                    'nl' => 'De reeks is over het jaar verspreid zodat de verleden-lijst iets te sorteren heeft, en de datumopmaak in meer dan één maand te zien is.',
                ],
                'head' => ['en' => 'Dates in two languages', 'nl' => 'Datums in twee talen'],
                'body' => [
                    'en' => '<p>Month and weekday names follow the language you are reading. Switch to Dutch in the top corner and this date changes along with the rest of the page.</p>',
                    'nl' => '<p>Maand- en weekdagnamen volgen de taal die je leest. Schakel rechtsboven naar Engels en deze datum verandert mee met de rest van de pagina.</p>',
                ],
            ],
            [
                'image' => 'abstract/02.jpg',
                'tags' => ['Dates & times'],
                'title' => ['en' => 'The oldest item in the set', 'nl' => 'Het oudste item van de reeks'],
                'intro' => [
                    'en' => 'Twelve events, six ahead and six behind. Enough to fill both halves of every row setting without anything looking staged.',
                    'nl' => 'Twaalf events, zes vooruit en zes terug. Genoeg om beide helften van elke rij-instelling te vullen zonder dat het in scène gezet oogt.',
                ],
                'head' => ['en' => 'Replace all of this', 'nl' => 'Vervang dit allemaal'],
                'body' => [
                    'en' => '<p>These items exist to show behaviour, not to be kept. Delete them, or edit them into your own programme — the reset puts them back a quarter of an hour after your last change.</p>',
                    'nl' => '<p>Deze items bestaan om gedrag te tonen, niet om te blijven. Verwijder ze of maak er je eigen programma van — de reset zet ze een kwartier na je laatste wijziging terug.</p>',
                ],
            ],
        ],
    ],

    // ── Reference ────────────────────────────────────────────────────────────
    // The generic archetype: no dates, ordered by hand. Deliberately terse — these
    // are reference cards, not the narrative the Features pages carry.
    'projects' => [
        [
            'image' => 'abstract/03.jpg',
            'tags' => ['Panel'],
            'title' => ['en' => 'Resource', 'nl' => 'Resource'],
            'intro' => [
                'en' => 'One class per model. Declares the fields; the panel builds the index, the editor, validation, search and filters from them.',
                'nl' => 'Eén class per model. Declareert de velden; het paneel bouwt daaruit de index, de editor, validatie, zoeken en filters.',
            ],
            'head' => ['en' => 'Generate the first version', 'nl' => 'Genereer de eerste versie'],
            'body' => [
                'en' => '<p><code>php artisan leap:module Page</code> reads the migration and the casts and writes it for you. Lives in <code>app/Leap/</code>.</p>',
                'nl' => '<p><code>php artisan leap:module Page</code> leest de migratie en de casts en schrijft hem voor je. Staat in <code>app/Leap/</code>.</p>',
            ],
        ],
        [
            'image' => 'abstract/04.jpg',
            'tags' => ['Panel'],
            'title' => ['en' => 'Attribute', 'nl' => 'Attribute'],
            'intro' => [
                'en' => 'One field, declared fluently. The input type and every rule about it hang off the same call.',
                'nl' => 'Eén veld, vloeiend gedeclareerd. Het invoertype en elke regel erover hangen aan dezelfde aanroep.',
            ],
            'head' => ['en' => 'A few of them', 'nl' => 'Een paar ervan'],
            'body' => [
                'en' => '<p><code>->translatable()</code>, <code>->required()</code>, <code>->unique()</code>, <code>->media()</code>, <code>->richtext()</code>, <code>->pivot()</code>, <code>->searchable()</code>, <code>->filterable()</code>, <code>->showIf()</code>.</p>',
                'nl' => '<p><code>->translatable()</code>, <code>->required()</code>, <code>->unique()</code>, <code>->media()</code>, <code>->richtext()</code>, <code>->pivot()</code>, <code>->searchable()</code>, <code>->filterable()</code>, <code>->showIf()</code>.</p>',
            ],
        ],
        [
            'image' => 'abstract/05.jpg',
            'tags' => ['Content'],
            'title' => ['en' => 'Section', 'nl' => 'Sectie'],
            'intro' => [
                'en' => 'A block of page content, stored as json. Declared once with its own fields, rendered by a Blade view of the same name.',
                'nl' => 'Een blok paginacontent, opgeslagen als json. Eén keer gedeclareerd met eigen velden, gerenderd door een gelijknamige Blade-view.',
            ],
            'head' => ['en' => 'Shipped with the template', 'nl' => 'Meegeleverd met de template'],
            'body' => [
                'en' => '<p>slide, text with image, quote, call to action, video, cookie overview, and a card row per content type.</p>',
                'nl' => '<p>slide, tekst met afbeelding, citaat, call to action, video, cookie-overzicht, en een kaartenrij per content-type.</p>',
            ],
        ],
        [
            'image' => 'abstract/06.jpg',
            'tags' => ['Media'],
            'title' => ['en' => 'Media', 'nl' => 'Media'],
            'intro' => [
                'en' => 'One record per file, attached to as many models as you like. Crop, focus point and alt text per language live on the record.',
                'nl' => 'Eén record per bestand, gekoppeld aan zoveel modellen als je wilt. Uitsnede, focuspunt en alt-tekst per taal staan op het record.',
            ],
            'head' => ['en' => 'On the page', 'nl' => 'Op de pagina'],
            'body' => [
                'en' => '<p>Served through a srcset of six widths, generated on first request. Width and height come from the record, so nothing shifts while loading.</p>',
                'nl' => '<p>Geserveerd via een srcset van zes breedtes, gegenereerd bij het eerste verzoek. Breedte en hoogte komen uit het record, dus er verschuift niets tijdens het laden.</p>',
            ],
        ],
        [
            'image' => 'abstract/07.jpg',
            'tags' => ['Content'],
            'title' => ['en' => 'Content type', 'nl' => 'Content-type'],
            'intro' => [
                'en' => 'A model registered in one place, after which the search, the sitemap, the page editor and the structured data all know about it.',
                'nl' => 'Een model dat op één plek geregistreerd staat, waarna zoeken, de sitemap, de pagina-editor en de gestructureerde data het allemaal kennen.',
            ],
            'head' => ['en' => 'Three archetypes', 'nl' => 'Drie archetypes'],
            'body' => [
                'en' => '<p><code>leap:content Recipe</code> generates it. News is ordered by a publication date, events by a date with optional times, generic by hand.</p>',
                'nl' => '<p><code>leap:content Recipe</code> genereert het. Nieuws wordt geordend op publicatiedatum, events op datum met optionele tijden, generiek met de hand.</p>',
            ],
        ],
        [
            'image' => 'abstract/08.jpg',
            'tags' => ['Panel'],
            'title' => ['en' => 'Role', 'nl' => 'Rol'],
            'intro' => [
                'en' => 'Permissions per module and per action. Without read on a module it is not in the menu and its URL is a 404.',
                'nl' => 'Rechten per module en per actie. Zonder leesrecht staat een module niet in het menu en is de URL een 404.',
            ],
            'head' => ['en' => 'Try it here', 'nl' => 'Probeer het hier'],
            'body' => [
                'en' => '<p>Log in as <code>editor@example.com</code> with the demo password: same panel, fewer modules.</p>',
                'nl' => '<p>Log in als <code>editor@example.com</code> met het demo-wachtwoord: hetzelfde paneel, minder modules.</p>',
            ],
        ],
        [
            'image' => 'abstract/01.jpg',
            'tags' => ['AI'],
            'title' => ['en' => 'AI task', 'nl' => 'AI-taak'],
            'intro' => [
                'en' => 'Three of them, each off until you name a provider, and each free to use a different one.',
                'nl' => 'Er zijn er drie, elk uit tot je een provider noemt, en elk vrij om een andere te gebruiken.',
            ],
            'head' => ['en' => 'What they do', 'nl' => 'Wat ze doen'],
            'body' => [
                'en' => '<p>Translate a field or a whole record, describe an image for its alt text, or generate an image from the record you are editing.</p>',
                'nl' => '<p>Een veld of een heel record vertalen, een afbeelding beschrijven voor de alt-tekst, of een afbeelding genereren vanuit het record dat je bewerkt.</p>',
            ],
        ],
        [
            'image' => 'abstract/02.jpg',
            'tags' => ['Privacy'],
            'title' => ['en' => 'Consent', 'nl' => 'Toestemming'],
            'intro' => [
                'en' => 'A bar, not a wall. Categories are allowed separately and third-party snippets stay parked until theirs is.',
                'nl' => 'Een balk, geen muur. Categorieën worden apart toegestaan en scripts van derden blijven geparkeerd tot die van hen aan de beurt is.',
            ],
            'head' => ['en' => 'One source', 'nl' => 'Eén bron'],
            'body' => [
                'en' => '<p>The cookie table on the Privacy page is rendered from the same config the banner enforces, so the two cannot disagree.</p>',
                'nl' => '<p>De cookietabel op de Privacy-pagina komt uit dezelfde config die de banner afdwingt, dus die twee kunnen niet van elkaar afwijken.</p>',
            ],
        ],
    ],
];
