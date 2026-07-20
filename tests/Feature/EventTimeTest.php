<?php

namespace Tests\Feature;

use App\Models\Event;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Event start and end times. The factory used to give every event 20:00–22:00, which
 * made a seeded set say nothing about how a time renders and never once produced the
 * one case the model has a rule for: an event that ends after midnight.
 */
class EventTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_times_fall_on_the_half_hour(): void
    {
        Event::factory()->count(20)->create()->each(function (Event $event): void {
            foreach ([$event->start_time, $event->end_time] as $time) {
                $this->assertMatchesRegularExpression('/^\d{2}:(00|30)/', $time, 'Expected a time on the half hour, got '.$time);
            }
        });
    }

    public function test_times_are_not_all_the_same(): void
    {
        $starts = Event::factory()->count(20)->create()->pluck('start_time')->unique();

        $this->assertGreaterThan(1, $starts->count(), 'Every seeded event started at the same time');
    }

    /**
     * The exception: an end at or before the start means the event finishes the next
     * day, and ends_at — which future()/past() query — has to say so.
     */
    public function test_an_event_running_past_midnight_ends_the_next_day(): void
    {
        $event = Event::factory()->pastMidnight()->create();

        $this->assertLessThan($event->start_time, $event->end_time, 'pastMidnight() must end at or before its start time');
        $this->assertTrue(
            $event->ends_at->isSameDay($event->date->copy()->addDay()),
            'Expected ends_at on the day after the event date, got '.$event->ends_at
        );
    }

    /**
     * The other branch: no end time means the event runs until the end of its own day,
     * so it stays upcoming all day rather than being over the moment it starts.
     */
    public function test_an_event_without_an_end_time_runs_until_the_end_of_its_day(): void
    {
        $event = Event::factory()->withoutEndTime()->create();

        $this->assertNull($event->end_time);

        // To the second: endOfDay() carries microseconds the datetime column does not.
        $this->assertSame(
            $event->date->copy()->endOfDay()->format('Y-m-d H:i:s'),
            $event->ends_at->format('Y-m-d H:i:s')
        );
    }

    /**
     * A card with no end time shows the start on its own — no dash trailing off into
     * nothing where the end would have been.
     */
    public function test_a_card_without_an_end_time_shows_only_the_start(): void
    {
        $event = Event::factory()->withoutEndTime()->create();

        // The event exists already, so the seeder adds none of its own — it runs here
        // only for the overview page the card hangs under and takes its URL from.
        $this->seed(EventSeeder::class);

        $html = $this->get($this->overviewPath('Events'))->assertOk()->getContent();

        $this->assertStringContainsString('<span class="item-time">'.substr($event->start_time, 0, 5).'</span>', $html);
    }

    /**
     * An overview shows upcoming events, so a seeded set dated at random around today
     * turned up in a different size on every reseed. The dates are deliberate now: six
     * upcoming every time, and six finished for a section set to past.
     */
    public function test_the_seeded_set_is_the_same_size_every_time(): void
    {
        $this->seed(EventSeeder::class);

        $this->assertCount(6, Event::active()->future()->get());
        $this->assertCount(6, Event::active()->past()->get());
    }

    /**
     * Being created is not the same as being seen: the two deliberate cases only set
     * times, so a random date could put either of them in the past and off the overview.
     */
    public function test_the_deliberate_cases_are_upcoming_and_so_actually_visible(): void
    {
        $this->seed(EventSeeder::class);

        $upcoming = Event::active()->future()->get();

        $this->assertNotEmpty($upcoming->filter(fn (Event $event): bool => $event->end_time <= $event->start_time));
        $this->assertNotEmpty($upcoming->filter(fn (Event $event): bool => empty($event->end_time)));
    }

    public function test_the_seeder_includes_an_event_without_an_end_time(): void
    {
        $this->seed(EventSeeder::class);

        $this->assertNotEmpty(
            Event::all()->filter(fn (Event $event): bool => empty($event->end_time)),
            'Expected at least one seeded event without an end time'
        );
    }

    public function test_an_ordinary_event_ends_on_its_own_day(): void
    {
        $event = Event::factory()->create(['start_time' => '20:00', 'end_time' => '22:00']);

        $this->assertTrue($event->ends_at->isSameDay($event->date));
    }

    /**
     * A seeded set must contain the late-night case rather than leave it to chance —
     * that is the whole reason the seeder asks for one explicitly.
     */
    public function test_the_seeder_includes_an_event_that_runs_past_midnight(): void
    {
        $this->seed(EventSeeder::class);

        $wrapping = Event::all()->filter(fn (Event $event): bool => $event->end_time <= $event->start_time);

        $this->assertNotEmpty($wrapping, 'Expected at least one seeded event running past midnight');
    }
}
