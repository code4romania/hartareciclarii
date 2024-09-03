<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Permission;
use App\Models\Point;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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

        $serviceTypes = ServiceType::query()
            ->with(['issueTypes', 'pointTypes'])
            ->get();

        foreach ($serviceTypes as $serviceType) {
            foreach ($serviceType->pointTypes as $pointType) {
                Point::factory(1000)
                    ->create([
                        'service_type_id' => $pointType->service_type_id,
                        'point_type_id' => $pointType->id,
                    ]);
            }
            foreach ($serviceType->issueTypes as $issueType) {
                $point = Point::where('service_type_id', $serviceType->id)->inRandomOrder()->first();
                $issue = Issue::factory()
                    ->create([
                        'service_type_id' => $point->service_type_id,
                        'point_id' => $point->id,
                    ]);

                $issue->issueTypes()->attach($issueType->id, ['value' => ['test' => 'test']]);
            }
        }

        Artisan::call('scout:rebuild');
    }
}
