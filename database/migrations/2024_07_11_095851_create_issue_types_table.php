<?php

declare(strict_types=1);

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
        Schema::create('issue_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ServiceType::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->timestamps();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreignIdFor(ServiceType::class)->after('point_id')
                ->constrained()
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_types');
    }
};
