<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\Point\Status;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\City;
use App\Models\County;
use App\Models\Material;
use App\Models\Point;
use App\Models\PointType;
use App\Models\Report;
use App\Models\ServiceType;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class PointsReports implements ShouldQueue
{
    use Queueable;

    private Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function handle(): void
    {
        $this->report->update(['status' => ReportStatus::IN_PROGRESS]);
        $filters = $this->report->filters;
        $dates = ['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']];
        $structure = $filters['structure'];
        $groupBy = $structure['group_by'];

        $query = Point::query()
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->when($structure['service_ids'], fn (Builder $q) => $q->whereIn('service_type_id', $structure['service_ids']))
            ->when($structure['point_type_ids'], fn (Builder $q) => $q->whereIn('point_type_id', $structure['point_type_ids']))
            ->when(! empty($structure['material_ids']), fn (Builder $q) => $q->whereHas('materials', fn (Builder $q) => $q->whereIn('material_id', $structure['material_ids'])))
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids']))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));

        if (! empty($structure['status'])) {
            foreach ($structure['status'] as $status) {
                match (Status::tryFrom($status)) {
                    Status::VERIFIED => $query->whereNotNull('verified_at'),
                    Status::UNVERIFIED => $query->whereNull('verified_at'),
                    Status::WITH_PROBLEMS => $query->whereHas('problems'),
                    default => null,
                };
            }
        }

        $query = match ($groupBy) {
            'service_type_id', 'point_type_id', 'city_id', 'county_id' => $query->select([DB::raw('count(*) as total'), $groupBy])->groupBy($groupBy),
            'status' => $query->select(
                DB::raw('count(*) as total'),
                DB::raw('IF(verified_at IS NULL, "unverified", IF(verified_at IS NOT NULL, "verified", IF((SELECT count(id) FROM problems WHERE problems.point_id = points.id) > 0, "with_problems", "verified"))) as compute_status')
            )->groupBy('compute_status'),
            'materials' => $query->select([DB::raw('count(*) as total'), 'model_has_materials.material_id'])
                ->join('model_has_materials', 'points.id', '=', 'model_has_materials.model_id')
                ->where('model_has_materials.model_type', (new Point())->getMorphClass())
                ->groupBy('model_has_materials.material_id'),
        };

        $groupBy = $groupBy === 'status' ? 'compute_status' : ($groupBy === 'materials' ? 'material_id' : $groupBy);

        $points = $query->get()->pluck('total', $groupBy);
        $names = match ($groupBy) {
            'service_type_id' => ServiceType::whereIn('id', $points->keys())->pluck('name', 'id'),
            'point_type_id' => PointType::whereIn('id', $points->keys())->pluck('name', 'id'),
            'city_id' => City::whereIn('id', $points->keys())->pluck('name', 'id'),
            'county_id' => County::whereIn('id', $points->keys())->pluck('name', 'id'),
            'compute_status' => Status::options(),
            'material_id' => Material::whereIn('id', $points->keys())->pluck('name', 'id'),
        };

        $data = $points->mapWithKeys(fn ($value, $id) => [$names[$id] => $value])->toArray();

        $this->report->update([
            'results' => $data,
            'label' => match ($groupBy) {
                'service_type_id' => __('report.column.service_type'),
                'point_type_id' => __('report.column.point_type'),
                'city_id' => __('report.column.city'),
                'county_id' => __('report.column.county'),
                'compute_status' => __('report.column.status'),
                'material_id' => __('report.column.materials'),
            },
            'status' => ReportStatus::COMPLETED,
        ]);

        Notification::make()
            ->title(__('report.notification.title'))
            ->body(__('report.notification.body'))
            ->actions([
                Action::make(__('report.action.export'))
                    ->url(ReportResource::getUrl('view', ['record' => $this->report]), true)
                    ->color('success'),
            ])
            ->success()
            ->send()
            ->toDatabase();
    }
}
