<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // User Management
            'user.list',
            'user.create',
            'user.edit',
            'user.delete',
            
            // Food Management
            'food.list',
            'food.create',
            'food.edit',
            'food.delete',
            
            // Food Category Management
            'food_category.list',
            'food_category.create',
            'food_category.edit',
            'food_category.delete',
            
            // Variant Management
            'variant.list',
            'variant.create',
            'variant.edit',
            'variant.delete',
            
            // Addon Management
            'addon.list',
            'addon.create',
            'addon.edit',
            'addon.delete',
            
            // Order Management
            'order.list',
            'order.create',
            'order.edit',
            'order.delete',
            'order.close',
            
            // Table Management
            'table.list',
            'table.create',
            'table.edit',
            'table.delete',
            
            // Menu Public View
            'menu.view',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $waiterRole = Role::create(['name' => 'waiter']);
        $waiterRole->givePermissionTo([
            // Order permissions
            'order.list',
            'order.create',
            'order.edit',
            'order.close',
            
            // Food management
            'food.list',
            'food.create',
            'food.edit',
            'food.delete',
            
            // Food category management
            'food_category.list',
            'food_category.create',
            'food_category.edit',
            'food_category.delete',
            
            // Variant management
            'variant.list',
            'variant.create',
            'variant.edit',
            'variant.delete',
            
            // Addon management
            'addon.list',
            'addon.create',
            'addon.edit',
            'addon.delete',
            
            // Table management (view and update status)
            'table.list',
            'table.edit',
            
            // Menu view
            'menu.view',
        ]);

        $cashierRole = Role::create(['name' => 'cashier']);
        $cashierRole->givePermissionTo([
            // Order permissions (view and close only)
            'order.list',
            'order.close',
            
            // View food data (read-only)
            'food.list',
            'food_category.list',
            'variant.list',
            'addon.list',
            
            // View tables
            'table.list',
            
            // Menu view
            'menu.view',
        ]);

        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $waiter = User::create([
            'name'     => 'Waiter',
            'email'    => 'waiter@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $waiter->assignRole('waiter');

        $cashier = User::create([
            'name'     => 'Cashier',
            'email'    => 'cashier@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $cashier->assignRole('cashier');

        $siti = User::create([
            'name'     => 'Siti Rahayu',
            'email'    => 'siti.waiter@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $siti->assignRole('waiter');

        $budi = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi.waiter@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $budi->assignRole('waiter');

        $rina = User::create([
            'name'     => 'Rina Marlina',
            'email'    => 'rina.cashier@dineflow.com',
            'password' => bcrypt('password'),
        ]);
        $rina->assignRole('cashier');
    }
}