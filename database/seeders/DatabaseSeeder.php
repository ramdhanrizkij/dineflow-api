<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            TableSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
