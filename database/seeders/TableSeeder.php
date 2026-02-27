<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\TableCategory;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $vipCategory = TableCategory::create(['name' => 'VIP']);
        $outdoorCategory = TableCategory::create(['name' => 'Outdoor']);
        $indorCategory = TableCategory::create(['name' => 'Indoor']);
        $tables = [
            ['code' => 'T-01', 'capacity' => 2, 'status' => 'occupied', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-02', 'capacity' => 2, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-03', 'capacity' => 2, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-04', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-05', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-06', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-07', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-08', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-09', 'capacity' => 6, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'T-10', 'capacity' => 6, 'status' => 'available', 'table_category_id' => $indorCategory->id],
            ['code' => 'O-01', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $outdoorCategory->id],
            ['code' => 'O-02', 'capacity' => 4, 'status' => 'available', 'table_category_id' => $outdoorCategory->id],
            ['code' => 'O-03', 'capacity' => 6, 'status' => 'available', 'table_category_id' => $outdoorCategory->id],
            ['code' => 'VIP-1', 'capacity' => 8, 'status' => 'available', 'table_category_id' => $vipCategory->id],
            ['code' => 'VIP-2', 'capacity' => 8, 'status' => 'reserved', 'table_category_id' => $vipCategory->id],
            ['code' => 'VIP-3', 'capacity' => 8, 'status' => 'available', 'table_category_id' => $vipCategory->id],
            ['code' => 'VIP-4', 'capacity' => 8, 'status' => 'available', 'table_category_id' => $vipCategory->id],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }
    }
}
