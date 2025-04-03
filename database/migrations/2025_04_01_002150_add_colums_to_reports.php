<?php

declare(strict_types=1);

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
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->id()->after('results');
            $table->dropColumn('title');
            $table->dropColumn('form_data');
            $table->json('filters');
            $table->string('label');
            $table->string('status')
                ->default('pending')
                ->after('id');
            $table->json('results')
                ->nullable()
                ->change();
            $table->foreignIdFor(App\Models\User::class, 'created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
};
