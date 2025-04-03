<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\Point\Status;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\City;
use App\Models\County;
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

    /**
     * Create a new job instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->report->update(['status' => ReportStatus::IN_PROGRESS]);
        $dates['start_date'] = $this->report->filters['start_date'];
        $dates['end_date'] = $this->report->filters['end_date'];
        $structure = $this->report->filters['structure'];
        $groupBy = $structure['group_by'];

        $query = Point::query()
            ->select([DB::raw('count(*) as total'), $groupBy])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])

            ->when(
                $structure['service_ids'],
                fn (Builder $query) => $query->whereIn('service_type_id', $structure['service_ids'])
            )
            ->when(
                $structure['point_type_ids'],
                fn (Builder $query) => $query->whereIn('point_type_id', $structure['point_type_ids'])
            )
            ->when(
                isset($structure['material_ids']) && \count($structure['material_ids']) > 0,
                fn (Builder $query) => $query->whereHas(
                    'materials',
                    fn (Builder $query) => $query->whereIn('material_id', $structure['material_ids'])
                )
            )
            ->when(
                $structure['city_ids'],
                fn (Builder $query) => $query->whereIn('city_id', $structure['city_ids'])
            )
            ->when(
                $structure['county_ids'],
                fn (Builder $query) => $query->whereIn('county_id', $structure['county_ids'])
            );

        if (isset($structure['status'])) {
            foreach ($structure['status'] as $status) {
                $status = Status::tryFrom($status);
                if ($status === Status::VERIFIED) {
                    $query->wherenotNull('verified_at');
                }
                if ($status === Status::UNVERIFIED) {
                    $query->whereNull('verified_at');
                }
                if ($status === Status::WITH_PROBLEMS) {
                    $query->whereHas('problems');
                }
            }
        }
        if ($groupBy === 'status') {
            $query->select(DB::raw('count(*) as total, IF(verified_at IS NULL, "unverified", IF(verified_at IS NOT NULL, "verified", IF((SELECT count(id) from problems where problems.point_id = points.id) > 0, "with_problems", "verified"))) as compute_status'))
                ->groupBy('compute_status');
            $groupBy = 'compute_status';
        } else {
            $query->groupBy($groupBy);
        }

        $points = $query->get()->pluck('total', $groupBy);
        $names = match ($groupBy) {
            'service_type_id' => ServiceType::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'point_type_id' => PointType::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'city_id' => City::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'county_id' => County::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'compute_status' => Status::options()
        };

        $data = [];
        foreach ($points as $id => $value) {
            $data[$names[$id]] = $value;
        }

        $key = match ($groupBy) {
            'service_type_id' => __('report.column.service_type'),
            'point_type_id' => __('report.column.point_type'),
            'city_id' => __('report.column.city'),
            'county_id' => __('report.column.county'),
            'compute_status' => __('report.column.status'),
        };
        $this->report->update([
            'results' => $data,
            'label' => $key,
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
