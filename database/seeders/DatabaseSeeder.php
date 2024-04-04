<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        $admin = User::factory()->create([
            'email' => 'email@example.com',
            'email_confirmed' => 1,
        ]);

        $role = Role::create(['id' => 1, 'name' => 'SuperAdmin', 'guard_name' => 'web']);
        foreach ($this->permissions as $permission_label) {
            Permission::create(['name' => $permission_label]);
        }

        $role->syncPermissions($this->permissions);

        $admin->assignRole('SuperAdmin');
        $this->call([
            FilterableFieldTypesSeeder::class,
            JudetGeoSeeder::class,
            FilterableFieldTypesSeeder::class,
            IssueTypeItemSeeder::class,
            IssueTypeSeeder::class,
            JudetGeoSeeder::class,
            MapPointFieldSeeder::class,
            MapPointServiceSeeder::class,
            MapPointTypeSeeder::class,
            RecycleMaterialAliasSeeder::class,
            RecycleMaterialSeeder::class,

        ]);
    }
}
