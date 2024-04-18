<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\City;
use App\Models\County;
use App\Models\MapPoint;
use App\Models\MapPointToField;
use Illuminate\Console\Command;

class MigrateCitiesAndCounties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harta:migrate-cities-and-counties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command migrate cities and counties from text format to ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Started');
        $this->info('Identifying counties by exact name match');

        $counties = County::all()->pluck('name', 'id')->toArray();
        $filledCounties = MapPointToField::whereHas('field', function ($query) {
            $query->where('field_name', 'county');
        })->pluck('value', 'recycling_point_id')->toArray();

        $validCounties = [];
        if (! empty($filledCounties)) {
            $this->info('Found ' . \count($filledCounties) . ' filled counties. Checking...');
            foreach ($filledCounties as $recycling_point_id => $county) {
                if (\in_array($county, $counties)) {
                    $validCounties[$recycling_point_id] = array_search($county, $counties);
                    unset($filledCounties[$recycling_point_id]);
                }
            }

            if (! empty($filledCounties)) {
                foreach ($filledCounties as $recycling_point_id => $county) {
                    foreach ($counties as $id_county => $existing_county) {
                        if ($this->_cleanUpDiacritice($county) == $this->_cleanUpDiacritice($existing_county)) {
                            $validCounties[$recycling_point_id] = $id_county;
                            unset($filledCounties[$recycling_point_id]);
                        }
                    }
                }
            }

            $this->info('Found ' . \count($validCounties) . ' valid counties. Updating...');
            if (! empty($validCounties)) {
                foreach ($validCounties as $recycling_point_id => $county_id) {
                    $point = MapPoint::where('id', $recycling_point_id)->first();
                    if ($point) {
                        $this->info('Updating info for point ' . $recycling_point_id . '...');
                        $point->id_county = $county_id;
                        $point->save();

                        $this->info('Removing old information for point ' . $recycling_point_id . '...');
                        $pointInfo = MapPointToField::where('recycling_point_id', $recycling_point_id)->whereHas('field', function ($query) {
                            $query->where('field_name', 'county');
                        })->first();
                        if ($pointInfo) {
                            $pointInfo->delete();
                        }
                    }
                }
                $this->info('Finished updating counties');
            }
        }

        $this->info('............................................');
        $this->info('Identifying cities by exact name match');

        $validCities = [];
        foreach ($counties as $id_county => $county) {
            $cities = City::where('county_id', $id_county)->pluck('name', 'id')->toArray();
            $filledCities = MapPointToField::select('field_type_recycling_point.*')
                ->whereHas('field', function ($query) {
                    $query->where('field_name', 'city');
                })
                ->join('recycling_points', 'recycling_points.id', '=', 'recycling_point_id')
                ->where('recycling_points.id_county', '=', $id_county)
                ->get()
                ->toArray();

            if (! empty($filledCities)) {
                foreach ($filledCities as $fieldCity) {
                    foreach ($cities as $id_city => $city) {
                        if ($this->_cleanUpDiacritice($fieldCity['value']) == $this->_cleanUpDiacritice($city)) {
                            $validCities[$fieldCity['recycling_point_id']] = $id_city;
                        }
                    }
                }
            }
        }

        $this->info('Found ' . \count($validCities) . ' valid cities. Updating...');
        if (! empty($validCities)) {
            foreach ($validCities as $recycling_point_id => $city_id) {
                $point = MapPoint::where('id', $recycling_point_id)->first();
                if ($point) {
                    $this->info('Updating info for point ' . $recycling_point_id . '...');
                    $point->id_city = $city_id;
                    $point->save();

                    $this->info('Removing old information for point ' . $recycling_point_id . '...');
                    $pointInfo = MapPointToField::where('recycling_point_id', $recycling_point_id)->whereHas('field', function ($query) {
                        $query->where('field_name', 'city');
                    })->first();
                    if ($pointInfo) {
                        $pointInfo->delete();
                    }
                }
            }
            $this->info('Finished updating cities');
        }

        $this->info('Done. Migration complete');
    }

    private function _cleanUpDiacritice($string)
    {
        $string = trim($string);
        $diacritice = [
            'ă' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ș' => 's',
            'ț' => 't',
            'ş' => 's',
            'Ă' => 'A',
            'Â' => 'A',
            'Î' => 'I',
            'Ș' => 'S',
            'Ț' => 'T',
            ' ' => '-',
            '\n' => '',
            '\t' => '',
            '\r\n' => '',
        ];

        foreach ($diacritice as $diacritic => $replacement) {
            $string = str_replace($diacritic, $replacement, $string);
        }

        return $string;
    }
}
