<?php

namespace App\Console\Commands;

use App\Models\Duplicate as DuplicateModel;
use Illuminate\Console\Command;

class SearchDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harta:search-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to find duplicates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Started');
        $table = (new DuplicateModel())->getTable();
        \DB::statement('TRUNCATE ' . $table);
        \DB::statement('
        	INSERT INTO ' . $table . ' (recycle_point_1, recycle_point_2, distance, point_type_id)
			(
				select
					rp.id as recycle_point_1,
					rp2.id as recycle_point_2,
					ST_Distance_Sphere(rp.location, rp2.location) as distance,
					rp.point_type_id
				from
					recycling_points rp ,
					recycling_points rp2
				where
					rp.id != rp2.id AND
					rp.point_type_id = rp2.point_type_id
				having
					distance < 10
			);


        	');

        $this->info('Done. Found ' . DuplicateModel::count() . ' possible duplicates');
    }
}
