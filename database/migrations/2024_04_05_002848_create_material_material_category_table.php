<?php

declare(strict_types=1);

use App\Models\Material;
use App\Models\MaterialCategory;
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
        Schema::create('material_material_category', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Material::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(MaterialCategory::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_material_category');
    }
};
