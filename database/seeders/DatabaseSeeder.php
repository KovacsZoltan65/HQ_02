<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SubdomainSeeder::class,

            RoleSeeder::class,              // OK
            PermissionSeeder::class,        // OK
            ModelHasRolesTableSeeder::class,
            ModelHasPermissionTableSeeder::class,   // OK
            RoleHasPermissionsTableSeeder::class,   // OK
            
            HqSettingsTableSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
