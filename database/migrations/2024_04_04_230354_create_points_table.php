<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\County;
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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('service_type');
            $table->string('point_type');
            $table->string('source');
            $table->foreignIdFor(County::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('notes')->nullable();
            $table->string('administered_by')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('observations')->nullable();
            $table->json('schedule')->nullable();
            $table->boolean('offers_money')->default(false);
            $table->boolean('offers_vouchers')->default(false);
            $table->boolean('offers_transport')->default(false);
            $table->boolean('free_of_charge')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
