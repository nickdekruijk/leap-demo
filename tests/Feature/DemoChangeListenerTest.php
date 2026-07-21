<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use NickDeKruijk\Leap\Models\Log;
use Tests\TestCase;

class DemoChangeListenerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['demo.enabled' => true, 'demo.cache_store' => 'array']);
    }

    protected function lastChange(): ?string
    {
        return Cache::store('array')->get(config('demo.cache_key'));
    }

    protected function demoUser(): User
    {
        return User::factory()->create(['email' => config('demo.email')]);
    }

    protected function makePage(): Page
    {
        $page = new Page;
        $page->title = ['en' => 'Scribble'];
        $page->save();

        return $page;
    }

    public function test_a_save_by_the_demo_user_arms_the_reset_timer(): void
    {
        $this->actingAs($this->demoUser(), config('leap.guard'));

        $this->makePage();

        $this->assertNotNull($this->lastChange());
    }

    public function test_a_delete_by_the_demo_user_arms_the_reset_timer(): void
    {
        $page = $this->makePage();
        $this->actingAs($this->demoUser(), config('leap.guard'));
        Cache::store('array')->forget(config('demo.cache_key'));

        $page->delete();

        $this->assertNotNull($this->lastChange());
    }

    public function test_a_save_by_another_user_does_not_arm_the_timer(): void
    {
        $this->actingAs(User::factory()->create(), config('leap.guard'));

        $this->makePage();

        $this->assertNull($this->lastChange());
    }

    public function test_a_leap_log_entry_does_not_arm_the_timer(): void
    {
        // Leap logs every action including logging in; a login alone must not
        // schedule a reset
        $this->actingAs($this->demoUser(), config('leap.guard'));
        Cache::store('array')->forget(config('demo.cache_key'));

        Log::create(['action' => 'login']);

        $this->assertNull($this->lastChange());
    }

    public function test_nothing_is_recorded_when_demo_mode_is_disabled(): void
    {
        config(['demo.enabled' => false]);
        $this->actingAs($this->demoUser(), config('leap.guard'));

        $this->makePage();

        $this->assertNull($this->lastChange());
    }
}
