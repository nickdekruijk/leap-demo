<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use NickDeKruijk\Leap\Models\Role;

class DemoUserSeeder extends Seeder
{
    /**
     * Create the demo account and give it the superuser role. The role_user
     * migration only attaches the first user that exists while migrating, so
     * on a fresh+seed run the attach must happen here explicitly.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => config('demo.email')],
            [
                'name' => 'Demo User',
                'password' => config('demo.password'),
            ],
        );

        Role::firstWhere('name', 'superuser')?->users()->syncWithoutDetaching([$user->id]);
    }
}
