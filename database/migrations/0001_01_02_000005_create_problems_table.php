<?php

declare(strict_types=1);

use App\Imports\ProblemTypesImport;
use App\Models\Point;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
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
        Schema::create('problem_types', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ProblemType::class, 'parent_id')
                ->nullable()
                ->constrained('problem_types')
                ->nullOnDelete();

            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('problem_type_service_type', function (Blueprint $table) {
            $table->foreignIdFor(ProblemType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(ServiceType::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->primary(['problem_type_id', 'service_type_id']);
        });

        Schema::create('problems', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Point::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class, 'reported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignIdFor(ProblemType::class, 'type_id')
                ->constrained('problem_types')
                ->cascadeOnDelete();

            $table->text('description')->nullable();
            $table->timestamps();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
        });

        Schema::create('problem_has_subtype', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Problem::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(ProblemType::class, 'subtype_id')
                ->constrained('problem_types')
                ->cascadeOnDelete();
        });

        Excel::import(new ProblemTypesImport, database_path('data/problem_types.csv'));
    }
};
