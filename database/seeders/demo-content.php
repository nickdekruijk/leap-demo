<?php

/**
 * Dutch and English copy for the demo site's content items, kept out of DemoSeeder
 * so the seeder stays mechanism. Every item has the same shape — title, intro and
 * the head/body of its one text section — so the seeder only has to know it once.
 *
 * The events are split into the two buckets the seeder dates differently: an item
 * under "future" announces something, one under "past" looks back at it.
 */
return [

    'news' => [
        [
            'title' => ['nl' => 'De werkplaats is verhuisd', 'en' => 'The workshop has moved'],
            'intro' => ['nl' => 'Vanaf deze maand staan de machines twee straten verderop, in een hal met daglicht en ruimte om te groeien.', 'en' => 'From this month the machines stand two streets away, in a hall with daylight and room to grow.'],
            'head' => ['nl' => 'Meer ruimte, meer licht', 'en' => 'More room, more light'],
            'body' => [
                'nl' => '<p>Na zeven jaar op de oude plek was de werkplaats uit haar jasje gegroeid. De nieuwe hal is bijna twee keer zo groot en heeft ramen op het noorden, wat prettig werkt bij het beoordelen van kleur.</p><p>De verhuizing is in één weekend gedaan, met dank aan de vijftien vrijwilligers die kwamen sjouwen.</p>',
                'en' => '<p>After seven years in the old place, the workshop had outgrown itself. The new hall is nearly twice the size and has north-facing windows, which helps when judging colour.</p><p>The move was done in a single weekend, thanks to the fifteen volunteers who came to carry.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Zomerprogramma is bekend', 'en' => 'Summer programme announced'],
            'intro' => ['nl' => 'Acht workshops, twee lezingen en elke donderdag een open avond waarop iedereen zonder aanmelding binnen kan lopen.', 'en' => 'Eight workshops, two talks and an open evening every Thursday that anyone can walk into without booking.'],
            'head' => ['nl' => 'Wat er te doen is', 'en' => 'What is on'],
            'body' => [
                'nl' => '<p>Het zwaartepunt ligt dit jaar op druktechnieken: zeefdruk, risografie en een korte serie over letterpers. Voor wie liever met hout werkt zijn er twee zaterdagen ingeruimd.</p><p>Aanmelden kan per workshop; de open avonden zijn vrij toegankelijk.</p>',
                'en' => '<p>The focus this year is on printing: screen printing, risograph and a short series on letterpress. For those who prefer wood, two Saturdays have been set aside.</p><p>Workshops are booked individually; the open evenings are free to attend.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Nieuwe zeefdrukpers in gebruik', 'en' => 'New screen printing press in use'],
            'intro' => ['nl' => 'De handpers uit 1978 gaat met pensioen. Zijn opvolger drukt tot A1 en is een stuk vriendelijker voor de rug.', 'en' => 'The 1978 hand press is retiring. Its successor prints up to A1 and is a good deal kinder to the back.'],
            'head' => ['nl' => 'Groter formaat, minder tillen', 'en' => 'Bigger format, less lifting'],
            'body' => [
                'nl' => '<p>De oude pers blijft staan voor kleine oplagen — hij doet het nog prima en veel bezoekers hebben er hun eerste druk op gemaakt.</p><p>Op de nieuwe pers passen affiches tot A1. De eerste serie hangt inmiddels in de gang.</p>',
                'en' => '<p>The old press stays put for small runs — it still works fine and many visitors made their first print on it.</p><p>The new press takes posters up to A1. The first series is already hanging in the corridor.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Terugblik op het voorjaarsseizoen', 'en' => 'Looking back at the spring season'],
            'intro' => ['nl' => 'Ruim vierhonderd bezoekers, negentien workshops en een markt die drukker was dan we hadden durven hopen.', 'en' => 'Over four hundred visitors, nineteen workshops and a market busier than we had dared hope.'],
            'head' => ['nl' => 'Het seizoen in cijfers', 'en' => 'The season in numbers'],
            'body' => [
                'nl' => '<p>Opvallend was de opkomst bij de doordeweekse avonden: die zaten vaker vol dan de zaterdagen. Voor het najaar verschuiven we daarom een deel van het programma naar de week.</p>',
                'en' => '<p>What stood out was the turnout on weekday evenings: they filled up more often than the Saturdays. For the autumn we are therefore moving part of the programme midweek.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Vrijwilligers gezocht voor de weekendopening', 'en' => 'Volunteers wanted for weekend opening'],
            'intro' => ['nl' => 'We willen op zondagmiddag open, en daarvoor zoeken we een handvol mensen die het leuk vindt om gastheer te zijn.', 'en' => 'We want to open on Sunday afternoons, and we are looking for a handful of people who enjoy playing host.'],
            'head' => ['nl' => 'Wat het inhoudt', 'en' => 'What it involves'],
            'body' => [
                'nl' => '<p>Een dienst duurt vier uur: koffie zetten, bezoekers wegwijs maken en aan het eind de deur op slot. Ervaring met de machines is niet nodig — die krijg je vanzelf.</p><p>Loop gerust een keer mee voor je besluit.</p>',
                'en' => '<p>A shift lasts four hours: making coffee, showing visitors around and locking up at the end. Experience with the machines is not needed — that comes along the way.</p><p>Feel free to tag along once before you decide.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Samenwerking met de bibliotheek', 'en' => 'Working together with the library'],
            'intro' => ['nl' => 'Vanaf het najaar staat er een kleine drukpers in de bibliotheek, en verzorgen wij daar maandelijks een workshop.', 'en' => 'From autumn a small press will stand in the library, and we will run a monthly workshop there.'],
            'head' => ['nl' => 'Buiten de eigen muren', 'en' => 'Beyond our own walls'],
            'body' => [
                'nl' => '<p>Niet iedereen weet de werkplaats te vinden, terwijl de bibliotheek voor veel mensen een vanzelfsprekende plek is. Door daar te gaan staan, bereiken we een publiek dat anders nooit een drukpers van dichtbij ziet.</p>',
                'en' => '<p>Not everyone finds their way to the workshop, while the library is an obvious place for many. By setting up there, we reach people who would otherwise never see a press up close.</p>',
            ],
        ],
    ],

    'events' => [

        'future' => [
            [
                'title' => ['nl' => 'Open werkplaats', 'en' => 'Open workshop'],
                'intro' => ['nl' => 'Loop binnen, kijk rond en probeer een techniek uit. Geen aanmelding, geen kosten.', 'en' => 'Walk in, look around and try a technique. No booking, no charge.'],
                'head' => ['nl' => 'Zomaar binnenlopen', 'en' => 'Just walk in'],
                'body' => [
                    'nl' => '<p>Elke machine staat aan en bij elke machine staat iemand die er raad mee weet. Neem gerust eigen werk mee als je ergens op vastloopt.</p>',
                    'en' => '<p>Every machine is running and beside every machine stands someone who knows their way around it. Bring your own work if you are stuck on something.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Workshop zeefdrukken', 'en' => 'Screen printing workshop'],
                'intro' => ['nl' => 'In één middag van ontwerp naar een eigen affiche in twee kleuren, gedrukt op de grote pers.', 'en' => 'From design to your own two-colour poster in a single afternoon, printed on the big press.'],
                'head' => ['nl' => 'Wat je leert', 'en' => 'What you will learn'],
                'body' => [
                    'nl' => '<p>Je belicht je eigen zeef, drukt een oplage van tien en neemt ze aan het eind van de middag mee naar huis. Materiaal zit bij de prijs in.</p><p>Maximaal acht deelnemers, dus er is ruim tijd per persoon.</p>',
                    'en' => '<p>You expose your own screen, print a run of ten and take them home at the end of the afternoon. Materials are included.</p><p>Eight participants at most, so there is plenty of time per person.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Lezing: kleur in de openbare ruimte', 'en' => 'Talk: colour in public space'],
                'intro' => ['nl' => 'Waarom is een stad grijs, en wat gebeurt er als je daar bewust van afwijkt?', 'en' => 'Why is a city grey, and what happens when you deliberately depart from that?'],
                'head' => ['nl' => 'Over de spreker', 'en' => 'About the speaker'],
                'body' => [
                    'nl' => '<p>Een uur over kleurgebruik in straten, gebouwen en bewegwijzering, met voorbeelden uit binnen- en buitenland. Na afloop is er ruimte voor vragen en een drankje.</p>',
                    'en' => '<p>An hour on the use of colour in streets, buildings and signage, with examples from home and abroad. Afterwards there is room for questions and a drink.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Repair café', 'en' => 'Repair café'],
                'intro' => ['nl' => 'Neem mee wat kapot is. Samen kijken we of het weer aan de praat te krijgen is.', 'en' => 'Bring whatever is broken. Together we will see if it can be got going again.'],
                'head' => ['nl' => 'Wat we kunnen maken', 'en' => 'What we can fix'],
                'body' => [
                    'nl' => '<p>Kleine elektronica, houten meubels, textiel en fietsen. Wat we niet kunnen repareren, halen we uit elkaar — ook dat leert iets.</p>',
                    'en' => '<p>Small electronics, wooden furniture, textiles and bicycles. Whatever we cannot repair, we take apart — that teaches something too.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Vrijdagmiddagborrel', 'en' => 'Friday afternoon drinks'],
                'intro' => ['nl' => 'De week afsluiten in de hal, met muziek en zo lang als het duurt.', 'en' => 'Closing the week in the hall, with music, for as long as it lasts.'],
                'head' => ['nl' => 'Tot laat', 'en' => 'Until late'],
                'body' => [
                    'nl' => '<p>Geen programma, geen eindtijd. Er is soep en iemand neemt een platenspeler mee.</p>',
                    'en' => '<p>No programme, no closing time. There is soup and someone is bringing a record player.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Nachtelijke drukmarathon', 'en' => 'Overnight print marathon'],
                'intro' => ['nl' => 'Van de avond tot diep in de nacht doorwerken aan één gezamenlijke oplage.', 'en' => 'Working from evening deep into the night on one shared print run.'],
                'head' => ['nl' => 'Hoe het werkt', 'en' => 'How it works'],
                'body' => [
                    'nl' => '<p>Iedereen ontwerpt één laag; aan het eind van de nacht is er een affiche waar alle deelnemers aan hebben meegewerkt. Slaapzak meenemen mag.</p>',
                    'en' => '<p>Everyone designs one layer; by the end of the night there is a poster every participant worked on. Bringing a sleeping bag is allowed.</p>',
                ],
            ],
        ],

        'past' => [
            [
                'title' => ['nl' => 'Voorjaarsmarkt', 'en' => 'Spring market'],
                'intro' => ['nl' => 'Dertig makers, twee straten en het mooiste weer dat we ooit voor een markt hebben gehad.', 'en' => 'Thirty makers, two streets and the best weather we have ever had for a market.'],
                'head' => ['nl' => 'Hoe het was', 'en' => 'How it went'],
                'body' => [
                    'nl' => '<p>Halverwege de middag was bij verschillende kramen de voorraad op. Volgend jaar staan we een straat verder door, zodat het minder vol loopt bij de ingang.</p>',
                    'en' => '<p>By mid-afternoon several stalls had sold out. Next year we will run one street further along, so it gets less crowded at the entrance.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Workshop boekbinden', 'en' => 'Bookbinding workshop'],
                'intro' => ['nl' => 'Twaalf deelnemers gingen naar huis met een eigen genaaid schetsboek.', 'en' => 'Twelve participants went home with a hand-sewn sketchbook of their own.'],
                'head' => ['nl' => 'Het resultaat', 'en' => 'The result'],
                'body' => [
                    'nl' => '<p>De Japanse bindwijze bleek makkelijker aan te leren dan gedacht. Er komt een vervolg waarin we met een harde kaft werken.</p>',
                    'en' => '<p>The Japanese binding turned out to be easier to learn than expected. A follow-up is coming in which we work with a hard cover.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Rondleiding voor scholen', 'en' => 'School tour'],
                'intro' => ['nl' => 'Twee brugklassen langs de machines, met aan het eind een eigen druk om mee te nemen.', 'en' => 'Two first-year classes along the machines, each leaving with a print of their own.'],
                'head' => ['nl' => 'Wat opviel', 'en' => 'What stood out'],
                'body' => [
                    'nl' => '<p>De vraag die het vaakst terugkwam: waarom zou je iets drukken als je het ook kunt printen? Daar is een middag over te praten.</p>',
                    'en' => '<p>The question that came back most: why print something by hand when a printer can do it? That is an afternoon of conversation in itself.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Lezing: duurzaam ontwerpen', 'en' => 'Talk: designing sustainably'],
                'intro' => ['nl' => 'Over materiaalkeuze, levensduur en de vraag of iets überhaupt gemaakt moet worden.', 'en' => 'On choosing materials, lifespan and whether a thing needs making at all.'],
                'head' => ['nl' => 'Nagesprek', 'en' => 'Afterwards'],
                'body' => [
                    'nl' => '<p>Het nagesprek duurde langer dan de lezing zelf. Een deel van de aanwezigen werkt inmiddels aan een gezamenlijk materialendepot.</p>',
                    'en' => '<p>The discussion afterwards lasted longer than the talk itself. Some of those present are now working on a shared materials depot.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Winterexpositie', 'en' => 'Winter exhibition'],
                'intro' => ['nl' => 'Werk van vijftien makers die het afgelopen jaar in de werkplaats te vinden waren.', 'en' => 'Work by fifteen makers who spent the past year in the workshop.'],
                'head' => ['nl' => 'De tentoonstelling', 'en' => 'The exhibition'],
                'body' => [
                    'nl' => '<p>Drie weken lang hing de hal vol: affiches, meubels, keramiek en een serie foto\'s van de verbouwing.</p>',
                    'en' => '<p>For three weeks the hall was full: posters, furniture, ceramics and a series of photographs of the rebuilding.</p>',
                ],
            ],
            [
                'title' => ['nl' => 'Nieuwjaarsontbijt', 'en' => 'New year\'s breakfast'],
                'intro' => ['nl' => 'Het jaar beginnen aan één lange tafel, met plannen die nog nergens op vastzitten.', 'en' => 'Starting the year at one long table, with plans not yet pinned to anything.'],
                'head' => ['nl' => 'Aan tafel', 'en' => 'At the table'],
                'body' => [
                    'nl' => '<p>Uit dit ontbijt kwamen twee van de projecten die inmiddels in uitvoering zijn. Volgend jaar zetten we de tafel weer neer.</p>',
                    'en' => '<p>Two of the projects now underway came out of this breakfast. Next year we will set the table again.</p>',
                ],
            ],
        ],
    ],

    'projects' => [
        [
            'title' => ['nl' => 'Herinrichting van het bibliotheekplein', 'en' => 'Redesign of the library square'],
            'intro' => ['nl' => 'Van doorgangsgebied naar plek om te blijven, met banken, bomen en bewegwijzering in één handschrift.', 'en' => 'From thoroughfare to a place to stay, with benches, trees and signage in one hand.'],
            'head' => ['nl' => 'De opgave', 'en' => 'The brief'],
            'body' => [
                'nl' => '<p>Het plein werd vooral gebruikt om overheen te lopen. Door de zitplekken uit de hoeken naar het midden te halen en de bomen door te zetten, is er schaduw waar mensen ook echt gaan zitten.</p><p>De bewegwijzering is in de werkplaats gedrukt en met de hand gemonteerd.</p>',
                'en' => '<p>The square was mostly used to walk across. By moving the seating from the corners into the middle and continuing the line of trees, there is now shade where people actually sit.</p><p>The signage was printed in the workshop and mounted by hand.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Huisstijl voor een buurtfestival', 'en' => 'Identity for a neighbourhood festival'],
            'intro' => ['nl' => 'Eén affiche dat in vier kleuren steeds anders is, zodat elke editie herkenbaar én nieuw blijft.', 'en' => 'One poster, different in four colours each time, so every edition stays recognisable and new.'],
            'head' => ['nl' => 'Uitgangspunt', 'en' => 'The starting point'],
            'body' => [
                'nl' => '<p>Het festival had geen budget voor een nieuwe campagne per jaar. De oplossing zit in het drukwerk: hetzelfde zeefdruk-ontwerp, elk jaar in een andere kleurcombinatie.</p>',
                'en' => '<p>The festival had no budget for a new campaign each year. The answer lies in the printing: the same screen-printed design, in a different colour combination every year.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Bewegwijzering in het stadspark', 'en' => 'Wayfinding in the city park'],
            'intro' => ['nl' => 'Twaalf palen die je de weg wijzen zonder dat je stil hoeft te staan om ze te lezen.', 'en' => 'Twelve posts that show the way without your having to stop to read them.'],
            'head' => ['nl' => 'Leesbaar in beweging', 'en' => 'Readable on the move'],
            'body' => [
                'nl' => '<p>De letters zijn groter dan gebruikelijk en de richtingen staan op ooghoogte van een fietser. Materiaal: hergebruikt hardhout uit de gemeentelijke opslag.</p>',
                'en' => '<p>The letters are larger than usual and the directions sit at a cyclist\'s eye level. Material: reclaimed hardwood from the council depot.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Jaarverslag in print en online', 'en' => 'Annual report in print and online'],
            'intro' => ['nl' => 'Een verslag dat je ook echt uitleest, met dezelfde opmaak op papier als op het scherm.', 'en' => 'A report you actually finish reading, laid out the same on paper as on screen.'],
            'head' => ['nl' => 'Twee dragers, één vorm', 'en' => 'Two carriers, one form'],
            'body' => [
                'nl' => '<p>De cijfers staan in de marge in plaats van in tabellen achterin, zodat ze naast het verhaal staan waar ze bij horen.</p>',
                'en' => '<p>The figures sit in the margin rather than in tables at the back, so they stand next to the story they belong to.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Meubels van reststromen', 'en' => 'Furniture from waste materials'],
            'intro' => ['nl' => 'Een serie tafels en krukken uit het hout dat bij verbouwingen overblijft.', 'en' => 'A series of tables and stools from the wood left over by building work.'],
            'head' => ['nl' => 'Van restpartij naar serie', 'en' => 'From offcuts to a series'],
            'body' => [
                'nl' => '<p>Omdat elke partij anders is, ligt niet de maat maar de verbinding vast: dezelfde pen-en-gatverbinding werkt op balken van uiteenlopende dikte.</p><p>De eerste serie staat in de kantine van de bibliotheek.</p>',
                'en' => '<p>Because every batch differs, it is not the size that is fixed but the joint: the same mortise and tenon works on beams of varying thickness.</p><p>The first series stands in the library canteen.</p>',
            ],
        ],
        [
            'title' => ['nl' => 'Tentoonstelling over lokale makers', 'en' => 'Exhibition on local makers'],
            'intro' => ['nl' => 'Portretten en werk van twintig mensen die binnen een kilometer van de werkplaats iets maken.', 'en' => 'Portraits and work by twenty people making something within a kilometre of the workshop.'],
            'head' => ['nl' => 'Opzet', 'en' => 'Set-up'],
            'body' => [
                'nl' => '<p>Elke maker kreeg één vitrine en één vraag: wat is het eerste dat je ooit hebt gemaakt? De antwoorden hingen ernaast, in handschrift.</p>',
                'en' => '<p>Each maker was given one display case and one question: what is the first thing you ever made? The answers hung beside them, handwritten.</p>',
            ],
        ],
    ],

];
