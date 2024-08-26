<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\County as CountyModel;
use App\Models\MapPointService as MapPointServiceModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportExport extends Model
{
    protected $table = 'imports';

    protected $casts = [
        'result' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(
            UserModel::class,
            'created_by',
            'id',
        );
    }

    public static function downloadMapPointSample()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Harta Reciclarii');
        $spreadsheet->getProperties()->setLastModifiedBy('Harta Reciclarii');
        $spreadsheet->getProperties()->setTitle('Map Point import example');
        $spreadsheet->getProperties()->setSubject('Map Point import example');
        $spreadsheet->getProperties()->setDescription('Map Point import example');
        $spreadsheet->setActiveSheetIndex(0);

        $data = json_decode('{"items":[{"tip_serviciu":"Colectare separată deșeuri","judet":"Cluj","oras":"Cluj Napoca","adresa":"Strada Gheorghe Dima 10, Cluj-Napoca 400000, Romania","latitude":"46.7548866","longitude":"23.5884752","location_notes":"Test notes","type":"Centru de colectare","materials":"Carton;Hârtie;Flacon plastic (ex: detergent, șampon s.a.);Alte tipuri de plastic;Tetra Pak (cutii de lapte, suc)","managed_by":"Administrat de S.C. Rosal Grup S.A./Sucursala Cluj Napoca","website":"http://rosalgrup.ro","email":"alt@email.com","phone_no":"0744555691","mon":"09:00-15:00","tue":"09:00-10:00","wed":"09:00-11:00","thu":"09:00-12:00","fri":"09:00-13:00","sat":"09:00-14:00","sun":"-","observatii":"notite 2","offers_money":"da","offers_transport":"Nu"},{"tip_serviciu":"Colectare separată deșeuri","judet":"Cluj","oras":"Cluj Napoca","adresa":"Strada Gheorghe Dima 10, Cluj-Napoca 400000, Romania","latitude":"46.755504","longitude":"23.5787266","location_notes":"-","type":"Container stradal","materials":"Carton;Hârtie;Flacon plastic (ex: detergent, șampon s.a.);Alte tipuri de plastic;Tetra Pak (cutii de lapte, suc)","managed_by":"Administrat de S.C. Rosal Grup S.A./Sucursala Cluj Napoca","website":"website","email":"aasdad@asdasd.com","phone_no":"-","mon":"09:00-15:00","tue":"09:00-10:00","wed":"09:00-11:00","thu":"09:00-12:00","fri":"09:00-13:00","sat":"09:00-14:00","sun":"-","observatii":"-","offers_money":"da","offers_transport":"Nu"}],"heading":["Tip serviciu","Judet","Oras","Adresa","Latitudine","Longitudine","Notite localizare (private)","Tip punct","Materiale colectate","Adminstrat de","Website","Email","Phone no.","Luni","Marti","Miercuri","Joi","Vineri","Sambata","Duminica","Observatii","Oferă bani","Oferă transport"]}', true);
        $contentArray = array_map(function ($value) {
            return ucwords(str_replace('_', ' ', $value));
        }, $data['heading']);
        $items = array_merge([$contentArray], $data['items']);

        $spreadsheet->getActiveSheet()->fromArray($items);
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        $filename = 'map-point-sample-import.xlsx';
        $storage_path = storage_path('/tmp/');
        if (! file_exists($storage_path)) {
            mkdir($storage_path);
        }
        $path = $storage_path . '/' . $filename;
        $writer->save($path);

        $returnArr = ['filename' => "$filename", 'content' => base64_encode(file_get_contents($path))];
        unlink($path);

        return $returnArr;
    }

    private static function _prepareMapPointExport($items)
    {
        $returnItems = [];
        $returnItems['items'] = [];
        $returnItems['heading'] = [
            'Tip serviciu',
            'Judet',
            'Oras',
            'Adresa',
            'Latitudine',
            'Longitudine',
            'Notite localizare (private)',
            'Tip punct',
            'Materiale colectate',
            'Adminstrat de',
            'Website',
            'Email',
            'Phone no.',
            'Orar',
            'Observatii',
            'Oferă bani',
            'Oferă transport',
        ];
        foreach ($items as $row) {
            $item = [];
            $item['tip_serviciu'] = $row->service->display_name;
            $item['judet'] = $row->county;
            $item['oras'] = $row->city;
            $item['adresa'] = $row->address;
            $item['latitude'] = $row->lat;
            $item['longitude'] = $row->lon;
            $item['location_notes'] = $row->location_notes;
            $item['type'] = $row->type->display_name;
            $item['materials'] = $row->materials;
            $item['managed_by'] = $row->managed_by;
            $item['website'] = $row->website;
            $item['email'] = $row->email;
            $item['phone_no'] = $row->phone_no;
            $item['opening_hours'] = json_encode($row->opening_hours);
            $item['observatii'] = $row->notes;
            $item['offers_money'] = $row->offers_money;
            $item['offers_transport'] = $row->offers_transport;

            $returnItems['items'][] = $item;
        }

        return $returnItems;
    }

    public static function validateAndMapImportField(Collection $mapPoint, &$errors, &$data): bool
    {
        if (! $mapPoint[0] || ! $service = MapPointServiceModel::whereDisplayName($mapPoint[0])->first()) {
            $errors[] = 'service_incorrect';
        } else {
            $data['service_id'] = $service->id;
        }
        if (! $mapPoint[1] || ! $county = CountyModel::where('name', $mapPoint[1])->first()) {
            $errors[] = 'county_incorrect';
        } else {
            $data['county'] = $county->id;
        }
        if (! $mapPoint[2]) {
            $errors[] = 'city_incorrect';
        } else {
            $data['city'] = $mapPoint[2];
        }
        if (! $mapPoint[3]) {
            $errors[] = 'address_incorrect';
        } else {
            $data['address'] = $mapPoint[3];
        }
        if (! $mapPoint[4] || ! self::validateLatitude($mapPoint[4])) {
            $errors[] = 'latitude_incorrect';
        } else {
            $data['lat'] = $mapPoint[4];
        }
        if (! $mapPoint[5] || ! self::validateLongitude($mapPoint[5])) {
            $errors[] = 'longitude_incorrect';
        } else {
            $data['lon'] = $mapPoint[5];
        }

        if (! $mapPoint[7]) {
            $errors[] = 'point_type_incorrect';
        } else {
            $type = MapPointTypeModel::whereDisplayName($mapPoint[7])->first();
            if (! $type || $type->service_id != $service->id) {
                $errors[] = 'point_type_incorrect';
            } else {
                $data['type_id'] = $type->id;
            }
        }
        if (data_get($data, 'lon') && data_get($data, 'lat') && data_get($data, 'type_id')) {
            $duplicates = \DB::select('
				select
					rp.id as recycle_point_1,
					ST_Distance_Sphere(rp.location, ' . \DB::raw('ST_GeomFromText("POINT(' . data_get($data, 'lon') . ' ' . data_get($data, 'lat') . ')")')->getValue(\DB::connection()->getQueryGrammar()) . ') as distance,
					rp.point_type_id
				from
					recycling_points rp
				where
					rp.point_type_id = ' . $type->id . '
				having
					distance < 10

        	');
            if (\count($duplicates) > 0) {
                foreach ($duplicates as $duplicate) {
                    $errors[] = 'possible_duplicate=>id|' . $duplicate->recycle_point_1;
                }
            }
        }

        if (! $mapPoint[8]) {
            $errors[] = 'materials_incorrect';
        } else {
            $materials = explode(';', $mapPoint[8]);
            foreach ($materials as $mat) {
                if (! $material = RecycleMaterialModel::where('name', $mat)->first()) {
                    $errors[] = 'material_not_found=>name|' . $mat;
                } else {
                    $data['materials'][] = $material->id;
                }
            }
        }
        $data['location_private_notes'] = (string) $mapPoint[6];
        $data['managed_by'] = (string) $mapPoint[9];
        $data['website'] = (string) $mapPoint[10];
        $data['email'] = (string) $mapPoint[11];
        $data['phone_no'] = (string) $mapPoint[12];
        $days = [
            'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
        ];
        for ($i = 0; $i < \count($days); $i++) {
            $hours = explode('-', preg_replace('/\s+/', '', $mapPoint[13 + $i]));
            $start_hours = '-';
            $end_hours = '-';
            $closed = true;
            if (\count($hours) > 1 && $hours[1] != '') {
                $start_hours = $hours[0];
                $end_hours = $hours[1];
                $closed = false;
            }
            $opening_hours[$days[$i]] = [
                'startDay' => $days[$i],
                'endDay' => $days[$i],
                'startHour' => $start_hours,
                'endHour' => $end_hours,
                'closed' => $closed,
            ];
        }
        $data['opening_hours'] = (array) $opening_hours;

        $data['notes'] = (string) $mapPoint[20];
        $data['offers_money'] = (strtolower(preg_replace('/\s+/', '', $mapPoint[21])) == 'da') ? 1 : 0;
        $data['offers_transport'] = (strtolower(preg_replace('/\s+/', '', $mapPoint[22])) == 'da') ? 1 : 0;

        return \count($errors) > 0 ? false : true;
    }

    public static function validateLatitude(float $lat)
    {
        return (preg_match("/\A[+-]?(?:90(?:\.0{1,18})?|\d(?(?<=9)|\d?)\.\d{1,18})\z/x", $lat)) ? true : false;
    }

    public static function validateLongitude($long)
    {
        return (preg_match("/\A[+-]?(?:180(?:\.0{1,18})?|(?:1[0-7]\d|\d{1,2})\.\d{1,18})\z/x", $long)) ? true : false;
    }
}
