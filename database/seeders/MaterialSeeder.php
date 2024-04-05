<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->getData();
        foreach ($data as $category) {
            $tmpCategory = MaterialCategory::create([
                'name' => $category['name'],
                'category_rank' => $category['rank'],
            ]);
            $materialsIds = [];

            foreach ($category['options'] as $option) {
                $materialsIds[] = Material::firstOrCreate(['name' => $option])->id;
            }
            $tmpCategory->materials()->sync($materialsIds);
        }
    }

    private function getData(): array
    {
        return [
            [
                'name' => 'Auto-moto',
                'rank' => 7,
                'options' => [
                    'Acumulatori auto',
                    'Anvelope',
                    'Ulei auto',
                    'Radiatoare',
                    'Vehicule scoase din uz',
                    'Filtre auto',
                    'Parbrize/ geamuri auto (sticlă)',
                    'Alte componente metalice, plastice automoto',
                ],
            ],

            [
                'name' => 'Construcții',
                'rank' => 8,
                'options' => [
                    'Azbest',
                    'Cărămizi, beton, moloz',
                    'Uși, grinzi, alte lemne din demolări',
                    'Tablă',
                    'Geamuri, polistiren, PVC (rame termopan)',
                ],
            ],

            [
                'name' => 'Lemn',
                'rank' => 9,
                'options' => [
                    'Mobilă din lemn',
                    'Rumeguș',
                    'Paleți',
                    'Brazi de Crăciun',
                    'Uși, grinzi, alte lemne din demolări',
                ],
            ],
            [
                'name' => 'Hârtie & carton',
                'rank' => 1,
                'options' => [
                    'Hârtie',
                    'Carton',
                ],

            ],
            [
                'name' => 'Deseuri de Echipamente Electrice și Electronice (DEEE)',
                'rank' => 4,
                'options' => [
                    'DEEE mici',
                    'DEEE mari',
                    'Telefoane mobile',
                    'Baterii',
                    'Becuri, neoane, LED',
                    'Cartușe și toner de imprimantă',
                ],

            ],
            [
                'name' => 'Plastic & PET',
                'rank' => 2,
                'options' => [
                    'PET',
                    'Flacon plastic (ex: detergent, șampon s.a.)',
                    'Folie plastic',
                    'Alte tipuri de plastic',
                    'Polistiren expandat',
                    'Tetra Pak (cutii de lapte, suc)',
                    'ABS',
                ],

            ],
            [
                'name' => 'Sticlă',
                'rank' => 5,
                'options' => [
                    'Sticle & borcane',
                    'Alte tipuri de sticlă',
                    'Parbrize / geamuri auto (sticlă)',
                ],

            ],
            [
                'name' => 'Metal & aluminiu',
                'rank' => 3,
                'options' => [
                    'Doze de aluminiu',
                    'Folie de aluminiu',
                    'Capsule de cafea',
                    'Tuburi de aluminiu cu aerosoli',
                    'Capace de metal',
                    'Cutii de conserve',
                    'Alamă',
                    'Alte obiecte din aluminiu',
                    'Bronz',
                    'Cupru',
                    'Fier',
                    'Fontă',
                    'Inox',
                    'Oțel',
                    'Plumb',
                    'Zinc',
                ],

            ],

            [
                'name' => 'Textile',
                'rank' => 10,
                'options' => [
                    'Haine',
                    'Incălțăminte',
                    'Genți',
                    'Deșeuri textile pre-producție',
                    'Alte textile',
                ],

            ],

            [
                'name' => 'Uleiuri & grăsimi',
                'rank' => 11,
                'options' => [
                    'Ulei alimentar uzat',
                    'Grăsimi alimentare',
                    'Ulei auto',
                    'Alte tipuri de uleiuri',
                ],

            ],

            [
                'name' => 'Compostabile',
                'rank' => 12,
                'options' => [
                    'Deșeuri organice (deseuri vegetale, resturi alimentare)',
                    'Deșeuri de grădină (iarbă tăiată, frunze uscate, crengi)',
                    'Brazi de Crăciun',
                ],

            ],

            [
                'name' => 'Periculoase',
                'rank' => 6,
                'options' => [
                    'Măști faciale',
                    'Solvenți',
                    'Vopsea',
                    'Pesticide',
                    'Erbicide',
                    'Îngrășăminte',
                    'Dispozitive medicale de uz personal',
                    'Tuburi de aluminiu cu aerosoli',
                ],

            ],
            [
                'name' => 'Altele',
                'rank' => 15,
                'options' => [
                    'ABS',
                    'Filtre de apă',
                    'Fibră optică',
                    'Mesh-uri publicitare',
                    'Jucării',
                ],

            ],
            [
                'name' => 'Voluminoase',
                'rank' => 13,
                'options' => [
                    'Saltele',
                    'Alte obiecte voluminoase',
                    'Mobilă din lemn',
                    'DEEE mari',
                    'Alte textile',
                ],

            ],

        ];
    }
}
