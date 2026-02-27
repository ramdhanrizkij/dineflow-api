<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['code' => 'T-01', 'capacity' => 2, 'status' => 'occupied'],
            ['code' => 'T-02', 'capacity' => 2, 'status' => 'available'],
            ['code' => 'T-03', 'capacity' => 2, 'status' => 'available'],
            ['code' => 'T-04', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'T-05', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'T-06', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'T-07', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'T-08', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'T-09', 'capacity' => 6, 'status' => 'available'],
            ['code' => 'T-10', 'capacity' => 6, 'status' => 'available'],
            ['code' => 'O-01', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'O-02', 'capacity' => 4, 'status' => 'available'],
            ['code' => 'O-03', 'capacity' => 6, 'status' => 'available'],
            ['code' => 'VIP-1', 'capacity' => 8, 'status' => 'available'],
            ['code' => 'VIP-2', 'capacity' => 8, 'status' => 'reserved'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
