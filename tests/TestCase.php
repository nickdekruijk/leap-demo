<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    /**
     * The path of a content type's overview page, in the active locale. The seeders
     * name that page after the translated type — Nieuws at /nieuws in Dutch, News at
     * /news in English — so a test that hardcodes /news passes or fails on which
     * language the site happens to be in. Ask for the path instead.
     *
     * @param  string  $type  The English title the seeder translates, e.g. 'News'.
     */
    protected function overviewPath(string $type): string
    {
        return '/'.Str::slug(__($type));
    }

    /**
     * The detail path of an item under that overview, e.g. /nieuws/mijn-bericht.
     */
    protected function itemPath(string $type, string $slug): string
    {
        return $this->overviewPath($type).'/'.$slug;
    }
}
