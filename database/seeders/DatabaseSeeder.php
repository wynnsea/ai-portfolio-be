<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Every seeder only fills empty tables, so this is safe to run on each deploy.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingSeeder::class,
            ProjectSeeder::class,
            CertificationSeeder::class,
            ActivitySeeder::class,
        ]);
    }
}
