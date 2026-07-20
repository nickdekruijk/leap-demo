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
