<?php

declare(strict_types=1);

use App\Models\Import;
use App\Models\ServiceType;
use App\Models\User;
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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ServiceType::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('file_name');
            $table->string('file_path');
            $table->string('importer');

            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('total_rows');
            $table->unsignedInteger('successful_rows')->default(0);
            $table->unsignedInteger('error_rows')->virtualAs('total_rows - successful_rows');

            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('failed_import_rows', function (Blueprint $table) {
            $table->id();
            $table->json('data');

            $table->foreignIdFor(Import::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->text('validation_error')->nullable();
            $table->timestamps();
        });
    }
};
