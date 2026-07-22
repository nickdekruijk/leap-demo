<?php

/**
 * What every seeded photograph should show. `demo:images` generates these once and
 * commits the result, so the demo host never needs an API key.
 *
 * The shared style is appended to every prompt: the whole set has to read as one
 * fictional workshop photographed on one afternoon, not as thirty stock photos.
 * Cards are cropped to 3:2 by the CSS, which is exactly the 1536x1024 the model
 * returns, so nothing is cut off.
 */
return [

    'style' => 'Documentary photograph in and around a community maker workshop in a Dutch city — '
        .'a former industrial hall and the brick streets, squares and parks surrounding it. '
        .'Natural light, muted honest colours, materials that have been used for years. '
        .'Unstaged, as if photographed on an ordinary weekday — where people appear they are '
        .'absorbed in what they are doing rather than posing for the camera. '
        .'No legible text or lettering anywhere, no logos or brand names. '
        .'One single photograph filling the whole frame — never a collage, grid or set of panels.',

    'size' => '1536x1024',

    'images' => [

        // ── News ──────────────────────────────────────────────────────────────
        'news/werkplaats-verhuisd.jpg' => [
            'prompt' => 'A large, almost empty workshop hall on moving day, tall north-facing windows along one wall, '
                .'a heavy printing press standing on pallets in the middle of the floor, stacked crates and rolled-up mats against the wall.',
        ],
        'news/zomerprogramma.jpg' => [
            'prompt' => 'A workbench with the makings of a season of workshops laid out: stacked screen printing frames, '
                .'a squeegee, tins of ink in a row, folded aprons and a pile of blank paper.',
        ],
        'news/nieuwe-zeefdrukpers.jpg' => [
            'prompt' => 'A new large-format screen printing press in a workshop, its steel arms and vacuum bed clean and unmarked, '
                .'standing beside an older, paint-spattered hand press that is clearly still in use.',
        ],
        'news/voorjaarsseizoen.jpg' => [
            'prompt' => 'A spring afternoon outside the workshop: people sitting on the low wall and doorstep with coffee, '
                .'a table of finished prints weighted down against the wind, blossom on a street tree, '
                .'the door propped open behind them.',
        ],
        'news/vrijwilligers-gezocht.jpg' => [
            'prompt' => 'A volunteer behind the welcoming counter of a workshop, pouring filter coffee for someone just arriving, '
                .'mismatched mugs on a shelf, a doormat by the open door.',
        ],
        'news/samenwerking-bibliotheek.jpg' => [
            'prompt' => 'A small tabletop printing press set up on a library table between tall bookshelves, '
                .'a tray of paper beside it, soft interior light from high windows.',
        ],

        // ── Events, upcoming ─────────────────────────────────────────────────
        'events/open-werkplaats.jpg' => [
            'prompt' => 'A workshop on an open evening: the wide entrance door rolled open to the street, work lamps lit, '
                .'a few visitors moving between the machines while a volunteer explains one of them.',
        ],
        'events/workshop-zeefdrukken.jpg' => [
            'prompt' => 'Someone pulling a squeegee across a screen printing frame, seen from the side over their shoulder, '
                .'a freshly printed two-colour poster appearing beneath the mesh, a short run drying alongside.',
        ],
        'events/lezing-kleur.jpg' => [
            'prompt' => 'An evening talk in a workshop hall: two rows of mismatched chairs with a dozen listeners seen from behind, '
                .'facing a lit projection wall with an abstract colour field on it, the machines pushed back into the shadows.',
        ],
        'events/repair-cafe.jpg' => [
            'prompt' => 'Two people bent over a dismantled desk lamp at a repair table, screwdrivers laid out in order beside them, '
                .'a soldering iron on its stand, a kettle and a bicycle wheel waiting their turn.',
        ],
        'events/vrijdagmiddagborrel.jpg' => [
            'prompt' => 'Outdoors on the brick square in front of the workshop at dusk: people standing around a trestle table '
                .'with a pot of soup and mugs, a record player on a crate, string lights strung to the building, '
                .'the lit doorway open behind them.',
        ],
        'events/drukmarathon.jpg' => [
            'prompt' => 'A workshop working late into the night, seen through its window from the dark street: '
                .'warm lamplight inside, two figures at a press, drying racks heavy with prints.',
        ],

        // ── Events, past ─────────────────────────────────────────────────────
        'events/voorjaarsmarkt.jpg' => [
            'prompt' => 'Outdoors in a city street on a bright spring day, photographed at eye level from the middle of the street: '
                .'a row of market stalls under simple white canopies along the brick pavement, ceramics and printed paper goods '
                .'on trestle tables, visitors browsing between them, brick facades and a tree in early leaf behind.',
        ],
        'events/workshop-boekbinden.jpg' => [
            'prompt' => 'Hands sewing the spine of a notebook with waxed thread, seen close from above, '
                .'a bone folder and an awl on the table, finished books with exposed stitching stacked beside them.',
        ],
        'events/rondleiding-scholen.jpg' => [
            'prompt' => 'A school group arriving outside the workshop on a grey morning: two teachers and a line of pupils '
                .'with backpacks waiting on the brick pavement by the open entrance, bicycles parked along the facade, '
                .'seen from across the street.',
        ],
        'events/lezing-duurzaam-ontwerpen.jpg' => [
            'prompt' => 'A table of material samples for a talk on sustainable design: offcuts of hardwood, '
                .'panels of pressed recycled plastic, sheets of unbleached board and a jar of wood shavings.',
        ],
        'events/winterexpositie.jpg' => [
            'prompt' => 'A workshop hall turned into an exhibition: framed prints hung in a line on a white brick wall, '
                .'a plywood plinth with a ceramic bowl, a stool as an exhibit, cold winter light from above.',
        ],
        'events/nieuwjaarsontbijt.jpg' => [
            'prompt' => 'A dozen people having breakfast at one long table down the middle of a workshop: bread boards, jam jars '
                .'and coffee pots between them, mismatched chairs, early morning light.',
        ],

        // ── Projects ─────────────────────────────────────────────────────────
        'projects/bibliotheekplein.jpg' => [
            'prompt' => 'A small city square outside a library after a redesign: long wooden benches in the middle rather than at the edges, '
                .'young lime trees in a row casting shade, brick paving, a plain wooden direction post with blank panels.',
        ],
        'projects/buurtfestival-huisstijl.jpg' => [
            'prompt' => 'Four screen printed posters of the same abstract geometric design pasted side by side on a plywood '
                .'hoarding in a city street, each in a different two-colour combination, no lettering on any of them, '
                .'wet pavement and passing cyclists.',
        ],
        'projects/bewegwijzering-stadspark.jpg' => [
            'prompt' => 'A tall weathered hardwood direction post standing at a fork in a park path, '
                .'its arms blank and unpainted, gravel underfoot and mature trees behind.',
        ],
        'projects/jaarverslag.jpg' => [
            'prompt' => 'A designer\'s desk seen from above: an open printed report with wide margins and grey blocks where text would be, '
                .'a laptop showing the same layout as blurred blocks, a ruler and a pencil.',
        ],
        'projects/meubels-reststromen.jpg' => [
            'prompt' => 'Three stools and a low table built from reclaimed beams of visibly different thicknesses, '
                .'joined with exposed mortise and tenon joints, in use in a busy library canteen with tall windows.',
        ],
        'projects/tentoonstelling-makers.jpg' => [
            'prompt' => 'A row of shallow glass display cases along a wall, each holding a single handmade object: '
                .'a carved spoon, a folded paper model, a small ceramic vessel, lit from above.',
        ],

        // ── Home page ────────────────────────────────────────────────────────
        // Slides carry white overlay text, so they need a calm, darker top half.
        'pages/home-slide-open.jpg' => [
            'prompt' => 'The workshop seen from the dark street on an autumn evening, its wide door standing open and '
                .'spilling warm light across the wet brick pavement, the lit interior small in the lower half of the frame '
                .'and the dark brick facade filling the upper half.',
        ],
        'pages/home-slide-leren.jpg' => [
            'prompt' => 'Someone showing another person how to hold a squeegee at a screen printing frame, seen from the side, '
                .'lit from below by a work lamp with the upper half of the frame in deep shadow.',
        ],
        'pages/home-slide-beheer.jpg' => [
            'prompt' => 'A tidy corner desk in a workshop at dusk: an open laptop seen from behind at an angle so the screen is not readable, '
                .'a notebook and a mug beside it, dark wall above.',
        ],
        'pages/home-wie-hier-komen.jpg' => [
            'prompt' => 'A mixed group at a workbench — a student, someone in their sixties, a parent with a teenager — '
                .'all working on their own thing side by side, aprons on, seen from a few steps back.',
        ],
        'pages/home-wat-er-staat.jpg' => [
            'prompt' => 'A tool wall of hand tools hung on a pegboard in careful order, outlines painted behind them, '
                .'a workbench with a vice in the foreground.',
        ],
        'pages/home-zelf-aan-de-slag.jpg' => [
            'prompt' => 'Wide view down a workshop between two rows of workbenches, machines on either side, '
                .'sawdust and paper offcuts on the floor, daylight from a row of high windows at the far end.',
        ],

        // ── Other pages ──────────────────────────────────────────────────────
        'pages/over-ons.jpg' => [
            'prompt' => 'A workshop that has clearly been used for years: layered paint marks on the benches, '
                .'a wall of labelled but unreadable storage drawers, tools worn smooth, warm afternoon light.',
        ],
        'pages/diensten.jpg' => [
            'prompt' => 'A stack of freshly printed posters on a workbench, only the top one visible and carrying '
                .'an abstract two-colour graphic with no lettering, a paper knife and a cutting mat beside it.',
        ],
        'pages/contact.jpg' => [
            'prompt' => 'The street entrance of a converted workshop building: a wide steel door standing open, '
                .'a bicycle parked against the brick, a plain blank sign bracket above the doorway.',
        ],

    ],
];
