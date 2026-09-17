<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Database\Seeders\AuthDatabaseSeeder;
use Modules\Content\Database\Seeders\ContentDatabaseSeeder;
use Modules\Service\Database\Seeders\ServiceDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AuthDatabaseSeeder::class,
            ServiceDatabaseSeeder::class,
            ContentDatabaseSeeder::class,
        ]);
    }
}
