<?php

declare(strict_types=1);

use App\Models\PointType;
use App\Models\ServiceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('point_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->timestamps();
        });
        Schema::table('points', function (Blueprint $table) {
            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(PointType::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });

        $this->seedData();
    }

    public function seedData(): void
    {
        $serviceTypes = [
            [
                'name' => 'Colectare separată deșeuri',
                'slug' => 'waste_collection',
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
                ]);

                $serviceType->pointTypes()->createMany(
                    collect($options['pointsTypes'])
                        ->map(fn (string $pointType) => ['name' => $pointType])
                );
                $serviceType->issueTypes()->createMany($options['issueTypes']);
            });
    }
};
