<?php

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
		Schema::create('filterable_field_types', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('service_id');
			$table->integer('field_type_id');
			$table->index(['service_id', 'field_type_id']);
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::dropIfExists('filterable_field_types');
    }
};
