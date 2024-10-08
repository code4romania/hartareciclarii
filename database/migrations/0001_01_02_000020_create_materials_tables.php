<?php

declare(strict_types=1);

use App\Imports\MaterialsImport;
use App\Models\Material;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('position');
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('model_has_materials', function (Blueprint $table) {
            $table->morphs('model');

            $table->foreignIdFor(Material::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('flag')->nullable();

            $table->primary(['material_id', 'model_id', 'model_type']);
        });

        Excel::import(new MaterialsImport, database_path('data/materials.csv'));
    }
};
