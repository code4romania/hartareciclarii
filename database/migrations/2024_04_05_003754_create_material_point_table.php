<?php

declare(strict_types=1);

use App\Models\Material;
use App\Models\Point;
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
        Schema::create('material_point', function (Blueprint $table) {
            $table->foreignIdFor(Material::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Point::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_point');
    }
};
