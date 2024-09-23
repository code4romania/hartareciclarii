<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Problem\ProblemType;
use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProblemTypesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        $serviceTypes = ServiceType::all();

        $collection
            ->reject(fn (Collection $row) => $row->get('parent_id'))
            ->each(function (Collection $row) use ($serviceTypes) {
                $problemType = $this->createProblemType($row);

                $ids = $serviceTypes
                    ->whereIn('slug', explode(',', (string) $row->get('service_types')))
                    ->pluck('id')
                    ->all();

                $problemType->serviceTypes()->attach($ids);
            });

        $mainProblemTypes = ProblemType::all();

        $collection
            ->filter(fn (Collection $row) => $row->get('parent_id'))
            ->each(fn (Collection $row) => $this->createProblemType($row, $mainProblemTypes));
    }

    protected function createProblemType(Collection $row, ?Collection $parents = null): ProblemType
    {
        return ProblemType::forceCreate([
            'code' => Str::after($row->get('id'), 'PRB_'),
            'name' => $row->get('name'),
            'slug' => $row->get('slug'),
            'created_at' => now(),
            'updated_at' => now(),
            'parent_id' => $parents
                ?->firstWhere('code', Str::after($row->get('parent_id'), 'PRB_'))
                ?->id,
        ]);
    }
}
