<?php

namespace Database\Seeders;

use App\Models\FilterablePointTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class FilterableFieldTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
	public function run(): void
	{
		Schema::withoutForeignKeyConstraints(function () {
			DB::unprepared(
				File::get(database_path('data/filterable_field_types.sql'))
			);
		});
	}
}
