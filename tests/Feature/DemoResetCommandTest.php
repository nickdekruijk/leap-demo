<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Deliberately no RefreshDatabase: the command runs migrate:fresh itself and
 * dropping tables inside RefreshDatabase's wrapping transaction breaks it.
 */
class DemoResetCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['demo.enabled' => true, 'demo.cache_store' => 'array']);
        Storage::fake('public');
    }

    protected function armTimer(): void
    {
        Cache::store('array')->forever(config('demo.cache_key'), now()->toIso8601String());
    }

    protected function seedUploads(): void
    {
        Storage::disk('public')->put(config('demo.baseline_files')[0], 'baseline');
        Storage::disk('public')->put('uploads/.gitignore', 'ignored');
        Storage::disk('public')->put('uploads/graffiti.jpg', 'demo user upload');
    }

    public function test_it_resets_once_the_demo_user_has_been_idle_long_enough(): void
    {
        $this->seedUploads();
        $this->armTimer();
        $this->travel(config('demo.reset_after') + 1)->minutes();

        $this->artisan('demo:reset')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => config('demo.email')]);
        $demoUserId = DB::table('users')->where('email', config('demo.email'))->value('id');
        $this->assertDatabaseHas('leap_role_user', ['user_id' => $demoUserId, 'role_id' => 1]);

        Storage::disk('public')->assertExists(config('demo.baseline_files')[0]);
        Storage::disk('public')->assertExists('uploads/.gitignore');
        Storage::disk('public')->assertMissing('uploads/graffiti.jpg');

        $this->assertNull(Cache::store('array')->get(config('demo.cache_key')));
    }

    public function test_it_removes_directories_the_demo_user_left_empty(): void
    {
        $this->seedUploads();
        Storage::disk('public')->put('scratch/deep/drawing.png', 'demo user upload');
        $this->armTimer();
        $this->travel(config('demo.reset_after') + 1)->minutes();

        $this->artisan('demo:reset')->assertSuccessful();

        $this->assertNotContains('scratch', Storage::disk('public')->allDirectories());
    }

    public function test_it_does_not_reset_while_the_last_change_is_recent(): void
    {
        $this->seedUploads();
        $this->armTimer();
        $this->travel(config('demo.reset_after') - 1)->minutes();

        $this->artisan('demo:reset')->assertSuccessful();

        // The debounce kicked in: no migration ran and uploads are untouched
        $this->assertFalse(Schema::hasTable('users'));
        Storage::disk('public')->assertExists('uploads/graffiti.jpg');
        $this->assertNotNull(Cache::store('array')->get(config('demo.cache_key')));
    }

    public function test_it_does_nothing_without_a_recorded_change(): void
    {
        $this->seedUploads();

        $this->artisan('demo:reset')->assertSuccessful();

        $this->assertFalse(Schema::hasTable('users'));
        Storage::disk('public')->assertExists('uploads/graffiti.jpg');
    }

    public function test_the_now_option_resets_without_a_recorded_change(): void
    {
        $this->seedUploads();

        $this->artisan('demo:reset --now')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => config('demo.email')]);
        Storage::disk('public')->assertMissing('uploads/graffiti.jpg');
    }

    public function test_it_refuses_to_run_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);

        $this->artisan('demo:reset --now')->assertFailed();

        $this->assertFalse(Schema::hasTable('users'));
    }
}
