<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Issue;
use App\Models\Material;
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
        Artisan::call('cache:clear');

        collect([
            Point::class,
            Material::class,
            City::class,
        ])->each(function (string $model) {
            Artisan::call('scout:delete-index', [
                'name' => (new $model)->searchableAs(),
            ]);

            $model::disableSearchSyncing();
        });

        Permission::insert(
            collect($this->permissions)
                ->map(fn (string $permission) => [
                    'name' => $permission,
                    'guard_name' => 'web',
                ])
                ->all()
        );

        $admin = User::factory(['email' => 'admin@example.com'])
            ->create();

        $admin->givePermissionTo($this->permissions);

        $serviceTypes = ServiceType::query()
            ->with(['issueTypes', 'pointTypes'])
            ->get();

        $cities = City::query()
            ->inRandomOrder()
            ->limit(100)
            ->get();

        $materials = Material::all();

//        foreach ($serviceTypes as $serviceType) {
//            $points = collect();

//            foreach ($serviceType->pointTypes as $pointType) {
//                $points->push(
//                    ...Point::factory(500)
//                        ->inCity($cities->random())
//                        ->withMaterials($materials->random(3))
//                        ->withType($serviceType, $pointType)
//                        ->create()
//                );
//            }

//            foreach ($serviceType->issueTypes as $issueType) {
//                $point = $points->random();
//
//                $issue = Issue::factory()
//                    ->create([
//                        'service_type_id' => $point->service_type_id,
//                        'point_id' => $point->id,
//                    ]);
//
//                $issue->issueTypes()->attach($issueType->id, ['value' => ['test' => 'test']]);
//            }
//        }

        Artisan::call('scout:rebuild');
    }
}
