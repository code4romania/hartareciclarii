<?php

declare(strict_types=1);

use App\Imports\PointTypesImport;
use App\Models\City;
use App\Models\County;
use App\Models\PointGroup;
use App\Models\PointType;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('point_types', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->timestamps();
        });

        Schema::create('points', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(County::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(City::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class, 'created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignIdFor(PointGroup::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // TODO: remove this
            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(PointType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status');
            $table->string('source');

            $table->string('address');
            $table->geometry('location', 'point')->nullable();
            $table->text('notes')->nullable();
            $table->string('administered_by')->nullable();
            $table->string('business_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('observations')->nullable();
            $table->text('schedule')->nullable();
            $table->boolean('offers_money')->nullable();
            $table->boolean('offers_vouchers')->nullable();
            $table->boolean('offers_transport')->nullable();
            $table->boolean('free_of_charge')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Excel::import(new PointTypesImport, database_path('data/point_types.csv'));
        // $this->seedData();
    }

    private function seedData(): void
    {
        $serviceTypes = [
            [
                'name' => 'Colectare separată deșeuri',
                'slug' => 'waste_collection',
                'has_dedicated_issues_tab' => true,
                'can_offer_money' => true,
                'can_offer_vouchers' => true,
                'can_offer_transport' => true,
                'can_have_business_name' => false,
                'can_collect_materials' => true,
                'pointsTypes' => [
                    'Container stradal',
                    'Punct magazin',
                    'Punct recuperare garanție (SGR)',
                    'Centru de aport voluntari',
                    'Centru de colectare',
                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Materialele colectate nu sunt corecte',
                        'slug' => 'PRB_Tip_03A',
                        'type' => 'materials',
                    ],
                    [
                        'name' => 'Programul nu este corect',
                        'slug' => 'PRB_Tip_04',
                        'type' => 'schedule',
                    ],
                    [
                        'name' => 'Containerul nu funcționează sau nu este bine întreținut',
                        'slug' => 'PRB_Tip_05',
                        'type' => 'container',
                    ],
                    [
                        'name' => 'Mi s-a refuzat preluarea deșeului',
                        'slug' => 'PRB_Tip_06A',
                        'type' => 'refusal',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
            [
                'name' => 'Reparații',
                'slug' => 'repairs',
                'can_request_payment' => true,
                'can_have_business_name' => true,
                'pointsTypes' => [
                    'Croitorie',
                    'Reparații încălțăminte',
                    'Ceasornicărie',
                    'Reparații bijuterii & bijutieri',
                    'Reparații electronice',
                    'Reparații electrocasnice',
                    'Reparații IT',
                    'Reparații telefoane',
                    'Reparații mobilă',
                    'Reparații biciclete',
                    'Reparații altele',

                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Produsele reparate nu sunt corect listate in descrierea punctului',
                        'slug' => 'PRB_Tip_03B',
                        'type' => 'materials',
                    ],
                    [
                        'name' => 'Programul nu este corect',
                        'slug' => 'PRB_Tip_04',
                        'type' => 'schedule',
                    ],
                    [
                        'name' => 'Mi s-a refuzat repararea produsului',
                        'slug' => 'PRB_Tip_06B',
                        'type' => 'refusal',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
            [
                'name' => 'Reutilizare',
                'slug' => 'reuse',
                'can_have_business_name' => true,
                'pointsTypes' => [
                    'Magazin haine second-hand',
                    'Magazin electronice second-hand',
                    'Magazin vintage & antichități',
                    'Anticariat',
                    'Consignație',
                    'Magazine cu ambalaje returnabile',
                    'Închiriere transport verde',
                    'Închiriere echipament sportiv',
                    'Închiriere obiecte pentru evenimente',
                    'Închiriere echipamente IT&C',
                    'Alte servicii de închiriere',
                    'Upcycling',
                    'Troc',
                    'Alte servicii de reutilizare',

                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Produsele reparate nu sunt corect listate in descrierea punctului',
                        'slug' => 'PRB_Tip_03B',
                        'type' => 'materials',
                    ],
                    [
                        'name' => 'Programul nu este corect',
                        'slug' => 'PRB_Tip_04',
                        'type' => 'schedule',
                    ],
                    [
                        'name' => 'Mi s-a refuzat repararea produsului',
                        'slug' => 'PRB_Tip_06B',
                        'type' => 'refusal',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
            [
                'name' => 'Reducere',
                'slug' => 'reduce',
                'can_have_business_name' => true,
                'pointsTypes' => [
                    'Magazin zero waste',
                    'Locație cu apă gratuită',
                    'Magazine cu vânzare vrac',
                    'Magazine care acceptă ambalaje proprii',
                    'Alte servicii de reducere',
                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
            [
                'name' => 'Donații',
                'slug' => 'donations',
                'can_offer_transport' => true,
                'can_have_business_name' => true,
                'pointsTypes' => [
                    'Centre de donații',
                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
            [
                'name' => 'Altele',
                'slug' => 'other',
                'can_have_business_name' => true,
                'pointsTypes' => [
                    'Altele',
                ],
                'issueTypes' => [
                    [
                        'name' => 'Adresa nu este corectă',
                        'slug' => 'PRB_Tip_01',
                        'type' => 'address',
                    ],
                    [
                        'name' => 'Locația punctului pe hartă nu este corectă',
                        'slug' => 'PRB_Tip_02',
                        'type' => 'location',
                    ],
                    [
                        'name' => 'Altă problemă',
                        'slug' => 'PRB_Tip_99',
                        'type' => 'other',
                    ],
                ],
            ],
        ];

        collect($serviceTypes)
            ->each(function (array $options) {
                $serviceType = ServiceType::create([
                    'name' => $options['name'],
                    'slug' => $options['slug'],

                    'has_dedicated_issues_tab' => data_get($options, 'has_dedicated_issues_tab', false),
                    'can_offer_money' => data_get($options, 'can_offer_money', false),
                    'can_offer_vouchers' => data_get($options, 'can_offer_vouchers', false),
                    'can_offer_transport' => data_get($options, 'can_offer_transport', false),
                    'can_request_payment' => data_get($options, 'can_request_payment', false),
                    'can_have_business_name' => data_get($options, 'can_have_business_name', false),
                    'can_collect_materials' => data_get($options, 'can_collect_materials', false),
                ]);

                $serviceType->pointTypes()->createMany(
                    collect($options['pointsTypes'])
                        ->map(fn (string $pointType) => ['name' => $pointType])
                );
                // $serviceType->issueTypes()->createMany($options['issueTypes']);
            });
    }
};
