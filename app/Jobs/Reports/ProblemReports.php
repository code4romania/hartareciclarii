<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\ProblemStatus;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\City;
use App\Models\County;
use App\Models\PointType;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
use App\Models\Report;
use App\Models\ServiceType;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProblemReports implements ShouldQueue
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

        $query = Problem::query()
            ->whereDate('problems.created_at', '>=', $dates['start_date'])
            ->whereDate('problems.created_at', '<=', $dates['end_date'])
            ->when($structure['service_ids'], fn (Builder $q) => $q->whereHas('point', fn (Builder $q) => $q->whereIn('service_type_id', $structure['service_ids'])))
            ->when($structure['point_type_ids'], fn (Builder $q) => $q->whereHas('point', fn (Builder $q) => $q->whereIn('point_type_id', $structure['point_type_ids'])))
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereHas('point', fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids'])))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereHas('point', fn (Builder $q) => $q->whereIn('city_id', $structure['county_ids'])))
            ->when($structure['problem_type_ids'], fn (Builder $q) => $q->whereIn('type_id', $structure['problem_type_ids']));

        $query = match ($groupBy) {
            'service_type_id', 'point_type_id', 'city_id', 'county_id' => $query->select([DB::raw('count(*) as total'), "points.$groupBy"])
                ->join('points', 'problems.point_id', '=', 'points.id')
                ->groupBy("points.$groupBy"),
            'problem_type_id' => $query->select([DB::raw('count(*) as total'), 'type_id as problem_type_id'])->groupBy('problem_type_id'),
            'status' => $query->select(
                DB::raw('count(*) as total'),
                DB::raw("CASE
                    WHEN started_at IS NULL AND closed_at IS NULL THEN 'new'
                    WHEN started_at IS NOT NULL AND closed_at IS NULL THEN 'pending'
                    WHEN started_at IS NOT NULL AND closed_at IS NOT NULL THEN 'closed'
                END AS compute_status")
            )->groupBy('compute_status'),
        };

        $groupBy = $groupBy === 'status' ? 'compute_status' : $groupBy;

        try {
            $problems = $query->get()->pluck('total', $groupBy);
            $names = match ($groupBy) {
                'service_type_id' => ServiceType::whereIn('id', $problems->keys())->pluck('name', 'id'),
                'point_type_id' => PointType::whereIn('id', $problems->keys())->pluck('name', 'id'),
                'city_id' => City::whereIn('id', $problems->keys())->pluck('name', 'id'),
                'county_id' => County::whereIn('id', $problems->keys())->pluck('name', 'id'),
                'problem_type_id' => ProblemType::whereIn('id', $problems->keys())->pluck('name', 'id'),
                'compute_status' => ProblemStatus::options(),
            };

            $data = $problems->mapWithKeys(fn ($value, $id) => isset($names[$id]) ? [$names[$id] => $value] : [])->toArray();

            $this->report->update([
                'results' => $data,
                'label' => match ($groupBy) {
                    'service_type_id' => __('report.column.service_type'),
                    'point_type_id' => __('report.column.point_type'),
                    'city_id' => __('report.column.city'),
                    'county_id' => __('report.column.county'),
                    'compute_status' => __('report.column.status'),
                    'problem_type_id' => __('report.column.problem_type'),
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
        } catch (\Exception $exception) {
            $this->report->update(['results' => [], 'label' => '', 'status' => ReportStatus::FAILED]);

            Notification::make()
                ->title(__('report.notification.title'))
                ->body(__('report.notification.body'))
                ->actions([
                    Action::make(__('report.action.export'))
                        ->url(ReportResource::getUrl('view', ['record' => $this->report]), true)
                        ->color('danger'),
                ])
                ->danger()
                ->send()
                ->toDatabase();
        }
    }
}
