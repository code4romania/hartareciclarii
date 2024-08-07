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
            $table->foreignIdFor(ServiceType::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('points', function (Blueprint $table) {
            $table->foreignIdFor(ServiceType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(PointType::class)->nullable()->constrained()->nullOnDelete();
        });

        $this->seedData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_types');
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['service_type_id']);
            $table->dropForeign(['point_type_id']);
        });
    }

    public function seedData(): void
    {
        $serviceTypes = [
            [
                'name' => 'Colectare separată deșeuri',
                'slug' => App\Enums\Point\ServiceType::WASTE_COLLECTION,
                'pointsTypes' => [
                    'Container stradal',
                    'Punct magazin',
                    'Punct recuperare garanție (SGR)',
                    'Centru de aport voluntari',
                    'Centru de colectare',
                ],
                'possibleIssues' => [
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
                'slug' => App\Enums\Point\ServiceType::REPAIRS,
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
                'possibleIssues' => [
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
                'slug' => App\Enums\Point\ServiceType::REUSE,
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
                'possibleIssues' => [
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
                'slug' => App\Enums\Point\ServiceType::REDUCTION,
                'pointsTypes' => [
                    'Magazin zero waste',
                    'Locație cu apă gratuită',
                    'Magazine cu vânzare vrac',
                    'Magazine care acceptă ambalaje proprii',
                    'Alte servicii de reducere',
                ],
                'possibleIssues' => [
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
                'slug' => App\Enums\Point\ServiceType::DONATIONS,
                'pointsTypes' => [
                    'Centre de donații',
                ],
                'possibleIssues' => [
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
                'slug' => App\Enums\Point\ServiceType::OTHER,
                'pointsTypes' => [
                    'Altele',
                ],
                'possibleIssues' => [
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

        foreach ($serviceTypes as $serviceType) {
            $service = ServiceType::create(['name' => $serviceType['name'], 'slug' => $serviceType['slug']]);
            foreach ($serviceType['pointsTypes'] as $pointType) {
                PointType::create(['name' => $pointType, 'service_type_id' => $service->id]);
            }
            foreach ($serviceType['possibleIssues'] as $issueType) {
                $issue = $service->issueTypes()->create($issueType);
            }
        }
    }
};
