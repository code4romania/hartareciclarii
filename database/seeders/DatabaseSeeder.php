<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Material;
use App\Models\Permission;
use App\Models\Point;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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

        $users = User::factory()
            ->count(50)
            ->create();

        $this->seedPoints($users);

        Artisan::call('scout:rebuild');
    }

    public function seedPoints(Collection $users): void
    {
        if (! env('SEED_POINTS', true)) {
            $this->command->warn('SEED_POINTS set to `false`, skipping seeding points...');

            return;
        }

        $serviceTypes = ServiceType::query()
            ->with(['problemTypes', 'pointTypes'])
            ->get();

        $cities = City::query()
            ->with('county')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $materials = Material::all();

        foreach ($serviceTypes as $serviceType) {
            Point::factory(100)
                ->inRandomCity($cities)
                ->withMaterials($materials)
                ->withType($serviceType)
                ->create();

            Point::factory(100)
                ->inRandomCity($cities)
                ->withMaterials($materials)
                ->withType($serviceType)
                ->withProblems($serviceType->problemTypes, $users)
                ->create();

            Point::factory(100)
                ->inRandomCity($cities)
                ->withMaterials($materials)
                ->withType($serviceType)
                ->unverified()
                ->createdByUser($users->random())
                ->create();
        }
    }
}
