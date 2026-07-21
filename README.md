<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About this project

Test/demo installation of [`nickdekruijk/leap`](https://github.com/nickdekruijk/leap) +
[`nickdekruijk/leap-template`](https://github.com/nickdekruijk/leap-template), deployed as the
public demo at [leap.nickdekruijk.nl](https://leap.nickdekruijk.nl).

### Demo mode

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

The seeded site is a fictional open workshop for printing, wood and repair. Its name is not
seeded but read from `APP_NAME` — the header, footer, page titles, `og:site_name` and the
admin panel all take it from there, and the seeded footer copyright bakes in whatever it says
at the moment of seeding. Set it on the demo host too, or the site introduces itself as
"Laravel".

`APP_LOCALE` is the language of the admin panel and the console, not of the website:
`config('leap.locales')` lists Dutch first, so the frontend serves Dutch on the unprefixed
URLs and English under `/en` whatever `APP_LOCALE` says. The demo runs it on `en` for an
English admin above a Dutch site. `tests/TestCase.php` pins the test locale to that first
frontend locale for the same reason — without it a test asks for `/news` while the page
lives at `/nieuws`, and every nested page and item detail comes back 404.

Run `php artisan demo:reset --now` to reset immediately, or
`php artisan migrate:fresh --seed --seeder="Database\Seeders\DemoSeeder"` to rebuild the demo
state without demo mode enabled. The demo host needs the standard scheduler cron:
`* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
