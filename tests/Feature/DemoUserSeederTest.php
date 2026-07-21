<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_database_seeder_creates_a_demo_superuser(): void
    {
        $this->seed();

        $user = User::firstWhere('email', config('demo.email'));
        $this->assertNotNull($user);
        $this->assertDatabaseHas('leap_role_user', ['user_id' => $user->id, 'role_id' => 1]);
    }

    public function test_seeding_twice_does_not_duplicate_the_demo_user(): void
    {
        $this->seed(DemoUserSeeder::class);
        $this->seed(DemoUserSeeder::class);

        $this->assertSame(1, User::where('email', config('demo.email'))->count());
        $this->assertSame(1, DB::table('leap_role_user')->where('role_id', 1)->count());
    }
}
