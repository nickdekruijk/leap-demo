<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A visitor arriving at the bare root has not said which language they want, but their
 * browser has. Every other URL says so in its own prefix, so only the root asks.
 */
class PreferredLocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    /**
     * @return array<int, string>
     */
    protected function locales(): array
    {
        return array_keys(config('leap.locales'));
    }

    public function test_a_browser_asking_for_the_other_language_is_sent_to_its_prefix(): void
    {
        [$default, $secondary] = $this->locales();

        $this->get('/', ['Accept-Language' => $secondary.'-BE,'.$secondary.';q=0.9,'.$default.';q=0.8'])
            ->assertRedirect('/'.$secondary);
    }

    public function test_a_browser_asking_for_the_default_language_stays_put(): void
    {
        [$default] = $this->locales();

        $this->get('/', ['Accept-Language' => $default.'-US,'.$default.';q=0.9'])->assertOk();
    }

    /**
     * Symfony's test client always sends an Accept-Language of its own, and it asks for
     * English — which on this site is the default locale, so a bare request is answered
     * rather than redirected. That is the same path a crawler takes: it sends no
     * preference, and the sitemap and hreflang alternates point it at the unprefixed
     * URLs, which therefore have to answer.
     */
    public function test_a_request_carrying_no_preference_of_its_own_is_answered(): void
    {
        $this->get('/')->assertOk();
    }

    /**
     * A language nobody here speaks is not a reason to move the visitor.
     */
    public function test_an_unknown_language_stays_put(): void
    {
        $this->get('/', ['Accept-Language' => 'ja-JP,ja;q=0.9'])->assertOk();
    }

    /**
     * Only the root asks; a deeper URL already carries its language.
     */
    public function test_a_deeper_page_is_never_redirected(): void
    {
        [$default, $secondary] = $this->locales();
        $page = Page::active()->whereNull('parent')->where('slug->'.$default, '!=', '/')->first();

        $this->assertNotNull($page, 'The seeder makes at least one page beside the homepage.');

        $this->get('/'.$page->slug, ['Accept-Language' => $secondary])->assertOk();
    }

    /**
     * Once a page has been read the language is settled, so a visitor who switches to
     * the default language by hand is not sent back on their next visit to the root.
     */
    public function test_a_visitor_who_has_already_read_a_page_stays_where_they_are(): void
    {
        [, $secondary] = $this->locales();

        $this->get('/')->assertOk();

        $this->get('/', ['Accept-Language' => $secondary.';q=0.9'])->assertOk();
    }

    /**
     * Which locale is unprefixed comes from the order of leap.locales, so nothing here
     * may assume it is English.
     */
    public function test_it_follows_the_configured_order_rather_than_a_hardcoded_language(): void
    {
        config(['leap.locales' => ['nl' => 'Nederlands', 'en' => 'English']]);

        $this->get('/', ['Accept-Language' => 'en-GB,en;q=0.9'])->assertRedirect('/en');

        $this->flushSession();

        $this->get('/', ['Accept-Language' => 'nl-NL,nl;q=0.9'])->assertOk();
    }

    /**
     * The root answers differently depending on a request header, so a shared cache
     * has to key on that header too.
     */
    public function test_the_root_varies_on_the_language_header(): void
    {
        $this->get('/')->assertHeader('Vary', 'Accept-Language');

        [, $secondary] = $this->locales();

        $this->flushSession();

        $this->get('/', ['Accept-Language' => $secondary])->assertHeader('Vary', 'Accept-Language');
    }
}
