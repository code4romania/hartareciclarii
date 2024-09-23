<?php

declare(strict_types=1);

use App\Imports\PointTypesImport;
use App\Models\City;
use App\Models\County;
use App\Models\Import;
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

            $table->foreignIdFor(Import::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

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
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
        });

        Excel::import(new PointTypesImport, database_path('data/point_types.csv'));
    }
};
