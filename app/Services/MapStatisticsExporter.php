<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Point\Status;
use App\Models\City;
use App\Models\County;
use App\Models\Point;
use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use ZipArchive;

class MapStatisticsExporter
{
    private const CHUNK_SIZE = 500;

    private const SERVICE_TYPE_ORDER = [
        'waste_collection',
        'repairs',
        'reuse',
        'reduce',
        'donations',
        'other',
    ];

    /**
     * Build a ZIP with puncte.csv, acoperire_judete.csv and acoperire_localitati.csv.
     */
    public function exportToZip(?string $zipPath = null): string
    {
        $zipPath ??= storage_path('app/exports/statistica-harta-' . now()->format('Y-m-d-His') . '.zip');

        File::ensureDirectoryExists(\dirname($zipPath));

        $workingDirectory = storage_path('app/exports/' . uniqid('map-stats-', true));
        File::ensureDirectoryExists($workingDirectory);

        try {
            $pointsPath = $workingDirectory . '/puncte.csv';
            $countiesPath = $workingDirectory . '/acoperire_judete.csv';
            $citiesPath = $workingDirectory . '/acoperire_localitati.csv';

            $this->writePointsCsv($pointsPath);
            $this->writeCountyCoverageCsv($countiesPath);
            $this->writeCityCoverageCsv($citiesPath);

            $this->zipFiles($zipPath, [
                'puncte.csv' => $pointsPath,
                'acoperire_judete.csv' => $countiesPath,
                'acoperire_localitati.csv' => $citiesPath,
            ]);
        } finally {
            File::deleteDirectory($workingDirectory);
        }

        return $zipPath;
    }

    private function writePointsCsv(string $path): void
    {
        $handle = $this->openCsv($path);

        fputcsv($handle, [
            'id',
            'tip serviciu',
            'tip punct',
            'judet',
            'localitate',
            'adresa',
            'latitudine',
            'longitudine',
            'materiale',
            'administrat de',
            'status',
            'grup',
        ]);

        Point::query()
            ->with([
                'serviceType:id,name',
                'pointType:id,name',
                'county:id,name',
                'city:id,name',
                'pointGroup:id,name',
                'materials:id,name',
            ])
            ->withCount('problems')
            ->chunkById(self::CHUNK_SIZE, function (Collection $points) use ($handle): void {
                foreach ($points as $point) {
                    fputcsv($handle, $this->pointRow($point));
                }
            });

        fclose($handle);
    }

    private function pointRow(Point $point): array
    {
        return [
            $point->id,
            $point->serviceType?->name,
            $point->pointType?->name,
            $point->county?->name,
            $point->city?->name,
            $point->address,
            $point->location?->latitude,
            $point->location?->longitude,
            $point->materials->pluck('name')->implode(', '),
            $point->administered_by,
            $this->pointStatusLabel($point),
            $point->pointGroup?->name,
        ];
    }

    private function pointStatusLabel(Point $point): string
    {
        if ($point->problems_count > 0) {
            return Status::WITH_PROBLEMS->getLabel();
        }

        if ($point->verified_at) {
            return Status::VERIFIED->getLabel();
        }

        return Status::UNVERIFIED->getLabel();
    }

    private function writeCountyCoverageCsv(string $path): void
    {
        $serviceTypes = $this->serviceTypes();
        $countsByCounty = $this->countsGroupedBy('county_id');

        $handle = $this->openCsv($path);
        fputcsv($handle, $this->coverageHeaders(['judet'], $serviceTypes));

        County::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->each(function (County $county) use ($handle, $serviceTypes, $countsByCounty): void {
                fputcsv($handle, $this->coverageRow(
                    [$county->name],
                    $countsByCounty->get($county->id, collect()),
                    $serviceTypes
                ));
            });

        fclose($handle);
    }

    private function writeCityCoverageCsv(string $path): void
    {
        $serviceTypes = $this->serviceTypes();
        $countsByCity = $this->countsGroupedBy('city_id');

        $cities = City::query()
            ->with('county:id,name')
            ->whereIn('id', $countsByCity->keys())
            ->get(['id', 'name', 'county_id'])
            ->sortBy(fn (City $city) => [
                $city->county?->name ?? '',
                $city->name,
            ])
            ->values();

        $handle = $this->openCsv($path);
        fputcsv($handle, $this->coverageHeaders(['judet', 'localitate'], $serviceTypes));

        foreach ($cities as $city) {
            fputcsv($handle, $this->coverageRow(
                [$city->county?->name, $city->name],
                $countsByCity->get($city->id, collect()),
                $serviceTypes
            ));
        }

        fclose($handle);
    }

    /**
     * @return Collection<int, Collection<int, int>>
     */
    private function countsGroupedBy(string $column): Collection
    {
        return Point::query()
            ->select([$column, 'service_type_id', DB::raw('COUNT(*) as total')])
            ->groupBy($column, 'service_type_id')
            ->get()
            ->groupBy(fn (Point $row) => (int) $row->{$column})
            ->map(fn (Collection $rows) => $rows->mapWithKeys(
                fn (Point $row) => [(int) $row->service_type_id => (int) $row->total]
            ));
    }

    private function serviceTypes(): Collection
    {
        return ServiceType::query()
            ->get(['id', 'name', 'slug'])
            ->sortBy(function (ServiceType $type): int {
                $position = array_search($type->slug, self::SERVICE_TYPE_ORDER, true);

                return $position === false ? 999 : $position;
            })
            ->values();
    }

    private function coverageHeaders(array $locationHeaders, Collection $serviceTypes): array
    {
        return [
            ...$locationHeaders,
            'total',
            ...$serviceTypes->pluck('name')->all(),
        ];
    }

    private function coverageRow(array $locationValues, Collection $countsByServiceType, Collection $serviceTypes): array
    {
        $categoryCounts = $serviceTypes
            ->map(fn (ServiceType $type) => (int) ($countsByServiceType[$type->id] ?? 0))
            ->all();

        return [
            ...$locationValues,
            array_sum($categoryCounts),
            ...$categoryCounts,
        ];
    }

    /**
     * @return resource
     */
    private function openCsv(string $path)
    {
        $handle = fopen($path, 'w');

        if ($handle === false) {
            throw new RuntimeException("Could not write CSV file: {$path}");
        }

        fwrite($handle, "\xEF\xBB\xBF");

        return $handle;
    }

    /**
     * @param array<string, string> $files filename in zip => absolute path
     */
    private function zipFiles(string $zipPath, array $files): void
    {
        $zip = new ZipArchive();
        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($result !== true) {
            throw new RuntimeException("Could not create ZIP archive: {$zipPath}");
        }

        foreach ($files as $name => $path) {
            $zip->addFile($path, $name);
        }

        $zip->close();
    }
}
