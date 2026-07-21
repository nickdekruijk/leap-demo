<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The frontend serves the first language of config('leap.locales') on the
     * unprefixed URLs and the others under a prefix. The tests walk those URLs and
     * read the titles behind them, so they have to speak that first language.
     *
     * APP_LOCALE is free to be another one — that is what the admin panel and the
     * console speak — and would otherwise send a test to /news while the page lives
     * at /nieuws. Pinning it here keeps the suite from passing or failing on which
     * language a developer happens to have in their .env.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ($locales = config('leap.locales')) {
            $this->app->setLocale(array_key_first($locales));
        }
    }
}
