<?php

namespace Database\Seeders;

use App\Models\MapPointField;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MapPointFieldSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		Schema::withoutForeignKeyConstraints(function () {
			DB::unprepared(
				File::get(database_path('data/field_types.sql'))
			);
		});
    }
}
