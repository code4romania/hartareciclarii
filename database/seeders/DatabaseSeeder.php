<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Permission;
use App\Models\Point;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $permissions = [
        'view_permissions',
        'view_roles',
        'view_admin_users',
        'manage_admin_users',
        'manage_roles',
        'manage_permissions',
        'view_recycle_materials',
        'manage_recycle_materials',
        'view_map_points',
        'manage_map_points',
        'view_imports',
        'manage_imports',
        'view_duplicates',
        'manage_duplicates',
        'view_reports',
        'manage_reports',
        'view_issues',
        'manage_issues',
        'admin_login',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $user = User::factory()
            ->create([
                'firstname' => 'Admin',
                'lastname' => 'User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);

        collect($this->permissions)->each(function ($permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',

            ]);
        });
        $user->givePermissionTo($this->permissions);

        Issue::factory(25)
            ->create();

        Point::factory(10)
            ->create();
    }
}
