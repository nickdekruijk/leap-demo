<?php

/**
 * The demo's placeholder images. `demo:images` generates these once and commits the
 * result, so the demo host is served them from the repository and needs no API key.
 *
 * They are deliberately abstract. This site is a starting point rather than a
 * finished website, and a photograph of something would suggest otherwise — while a
 * flat composition in the site's own accent colour reads as "put your own image
 * here", proves the whole media pipeline anyway, and never goes out of date.
 *
 * Thirteen images for thirty-odd places: the cards reuse them, which is honest for
 * placeholders and shows that one media record can hang off many records at once.
 *
 * Cards are cropped to 3:2 by the CSS, which is exactly the 1536x1024 the model
 * returns, so nothing is cut off.
 */
return [

    'style' => 'Flat abstract graphic composition, vector-like, no photography, no text or lettering of any kind. '
        .'Built from simple geometric shapes — circles, arcs, quarter-rounds, straight bands — with generous empty space. '
        .'Palette strictly limited to deep green #1f7a4d, off-white #f7f7f5 and near-black #191919, plus at most one muted '
        .'supporting tone. Matte, even, print-like surface with no gradients, no shadows, no 3D and no glow.',

    'size' => '1536x1024',

    'images' => [

        // Three carousel slides. Mostly dark, because white text sits on top of them.
        'slides/01.jpg' => [
            'prompt' => 'Near-black background with a single large deep green arc sweeping in from the lower right corner, '
                .'and a thin off-white line crossing it. The upper half stays almost empty and very dark.',
        ],
        'slides/02.jpg' => [
            'prompt' => 'Near-black background with a loose grid of small off-white squares in the lower right, '
                .'growing further apart towards the top left, where the frame is empty and dark.',
        ],
        'slides/03.jpg' => [
            'prompt' => 'Near-black background with three concentric deep green half-circles nested in the bottom left corner, '
                .'the rest of the frame empty and dark.',
        ],

        // Full-width places: the bottom-wide image on the home page and the background
        // image behind a text section.
        'wide/01.jpg' => [
            'prompt' => 'Off-white background with a wide horizontal band of deep green across the lower third, '
                .'interrupted by two tall near-black rectangles standing on it like columns.',
        ],
        'wide/02.jpg' => [
            'prompt' => 'Deep green field filling the frame, with a single large off-white circle low on the right '
                .'and a near-black quarter-round in the top left corner. Even and calm, meant to sit behind white text.',
        ],

        // Eight card-sized compositions, reused across the content items.
        'abstract/01.jpg' => [
            'prompt' => 'Off-white background with one large deep green circle slightly left of centre, '
                .'overlapped by a near-black rectangle standing on its right edge.',
        ],
        'abstract/02.jpg' => [
            'prompt' => 'Off-white background with four deep green vertical bands of increasing width, '
                .'and a single near-black dot resting between the two widest.',
        ],
        'abstract/03.jpg' => [
            'prompt' => 'Off-white background with a near-black arc opening upwards, like a wide shallow bowl, '
                .'holding one small deep green square above its centre.',
        ],
        'abstract/04.jpg' => [
            'prompt' => 'Deep green background with an off-white staircase of three flat steps rising from the lower left '
                .'and one thin near-black line running horizontally behind it.',
        ],
        'abstract/05.jpg' => [
            'prompt' => 'Off-white background with two overlapping quarter-rounds, one deep green and one near-black, '
                .'meeting in the centre and leaving the corners empty.',
        ],
        'abstract/06.jpg' => [
            'prompt' => 'Near-black background with a single deep green diagonal band from the lower left to the upper right, '
                .'crossed by a narrow off-white strip.',
        ],
        'abstract/07.jpg' => [
            'prompt' => 'Off-white background with a loose column of five deep green circles of decreasing size, '
                .'and one near-black circle of the largest size at the bottom right.',
        ],
        'abstract/08.jpg' => [
            'prompt' => 'Off-white background with a large near-black square rotated slightly off true, '
                .'and a deep green half-circle emerging from behind its upper edge.',
        ],

    ],
];
