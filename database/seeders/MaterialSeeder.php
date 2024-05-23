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
}
