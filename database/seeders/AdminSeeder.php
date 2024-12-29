<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_roles',
            'manage_settings',
            'manage_content',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }

        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'admin']);
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
         
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole->givePermissionTo([
            'view_dashboard',
            'manage_settings',
            'manage_content',
        ]);

       // Create super admin user
       $superAdmin = Admin::create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('password'),
        ]);

        $superAdmin->assignRole('super-admin');

        // Create regular admin user
        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
    }
}
