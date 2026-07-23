# Leap demo

Test/demo installation of [`nickdekruijk/leap`](https://github.com/nickdekruijk/leap) +
[`nickdekruijk/leap-template`](https://github.com/nickdekruijk/leap-template), deployed as the
public demo at [leap.nickdekruijk.nl](https://leap.nickdekruijk.nl).

It is a plain Laravel application with both packages installed, so it doubles as a starting
point: clone it, replace the seeded content, and build your own site on top.

## Installation

```bash
git clone https://github.com/nickdekruijk/leap-demo.git
cd leap-demo
composer setup
```

`composer setup` installs the dependencies, copies `.env.example` to `.env`, generates the
app key, runs the migrations and builds the frontend assets. Point `DB_*` at a database
first — the default is MySQL on `127.0.0.1`. Then seed the demo content:

```bash
php artisan migrate:fresh --seed --seeder="Database\Seeders\DemoSeeder"
```

Log in at `/admin` with `info@example.com` and the password from `DEMO_PASSWORD`
(`leapdemo` by default).

## Demo mode

With `DEMO_MODE=true` the site acts as a public playground: every Eloquent write made by the
demo account moves a last-change marker (see `app/Listeners/RecordDemoChange.php`), and the
scheduled `demo:reset` command rebuilds the database and cleans the public upload disk once
the site has been untouched for `DEMO_RESET_AFTER` minutes — a debounce, not a fixed cycle.
Settings live in `config/demo.php`:

| env | default | |
|---|---|---|
| `DEMO_MODE` | `false` | enable demo mode (listener, schedule, reset command) |
| `DEMO_EMAIL` | `info@example.com` | demo account, seeded with the superuser role |
| `DEMO_PASSWORD` | `leapdemo` | publicly documented demo password |
| `DEMO_RESET_AFTER` | `15` | idle minutes before the reset fires |
| `DEMO_CACHE_STORE` | `file` | marker store; must survive `migrate:fresh` |

The rebuild runs `database/seeders/DemoSeeder.php` as its root seeder, not `DatabaseSeeder`:
that one seeder holds the demo's entire state — its admin account, pages, tags and the Dutch
and English content in `database/seeders/demo-content.php`. `PageSeeder` and the content
seeders are left exactly as `leap:template` ships them and are unused here, so the demo can
carry its own copy without drifting from the template.

The seeded site documents itself: every page shows one capability of the panel or the
template and explains where to change it, because this is a starting point to build on
rather than a finished website. English is served on the bare URLs and Dutch under
`/nl` — swap the order of `leap.locales` to swap that around. The images are deliberately
abstract placeholders. `APP_NAME` still names the site in the header, the footer, the page
titles and the admin panel, so set it on the demo host too.

Two accounts are seeded: `info@example.com` is a superuser, and `editor@example.com` (same
password) has a role without users and roles, so the difference is visible. The AI
translation and alt-text tasks are on and need an `OPENAI_API_KEY`; image generation is
deliberately off, because a click on it costs money and every visitor shares one account.

`APP_LOCALE` is the language of the admin panel and the console, not of the website: the
frontend serves the first language of `config('leap.locales')` on the unprefixed URLs and
the rest under a prefix, whatever `APP_LOCALE` says. `tests/TestCase.php` pins the test
locale to that first frontend locale, so the suite does not pass or fail on which language
a developer happens to have in their `.env`.

Run `php artisan demo:reset --now` to reset immediately, or
`php artisan migrate:fresh --seed --seeder="Database\Seeders\DemoSeeder"` to rebuild the demo
state without demo mode enabled. The demo host needs the standard scheduler cron:
`* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`

## Tests

```bash
php artisan test
```

## Issues

Bugs and questions about the admin panel itself belong in
[`nickdekruijk/leap`](https://github.com/nickdekruijk/leap/issues), and those about the site
template in [`nickdekruijk/leap-template`](https://github.com/nickdekruijk/leap-template/issues).
This repository only tracks the demo application.

## License

Open-sourced software licensed under the [MIT license](LICENSE).
