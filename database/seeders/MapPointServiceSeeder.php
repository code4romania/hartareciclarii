<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MapPointServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		Schema::withoutForeignKeyConstraints(function () {
			DB::unprepared(
				File::get(database_path('data/recycling_point_services.sql'))
			);
		});
    }
}