<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@example.com',
            'role'  => 'admin',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name'  => 'Moderator One',
            'email' => 'mod1@example.com',
            'role'  => 'moderator',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name'  => 'Moderator Two',
            'email' => 'mod2@example.com',
            'role'  => 'moderator',
            'password' => 'password',
        ]);

        User::factory(12)->create(['role' => 'user']);

        $this->call([
            DestinationSeeder::class,
            PlaceSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
