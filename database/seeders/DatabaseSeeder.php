<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Point;
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
//        User::factory(10)->create();
//        $this->call(
//            MaterialSeeder::class
//        );

        Point::factory(100)

            ->create();
    }
}
