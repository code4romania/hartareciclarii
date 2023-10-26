<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $user = \App\Models\User::find(1);
		
		$role = Role::create(['id' => 1, 'name' => 'SuperAdmin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'view_roles']);
        $role->givePermissionTo($permission);
        $permission = Permission::create(['name' => 'view_permissions']);
        $role->givePermissionTo($permission);
        $permission = Permission::find(1);
        $role->givePermissionTo($permission);

        // $role->givePermissionTo($permission);
        // $permission->assignRole($role);
        // $role->syncPermissions($permissions);
        // $permission->syncRoles($roles);
    }
}
