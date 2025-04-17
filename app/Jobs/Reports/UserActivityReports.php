<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\ProblemStatus;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\City;
use App\Models\County;
use App\Models\Point;
use App\Models\PointType;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
use App\Models\Report;
use App\Models\ServiceType;
use Filament\Notifications\Actions\Action;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Notification;

class UserActivityReports implements ShouldQueue
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
        $dates = [
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
        ];
        $structure = $filters['structure'];
        $groupBy = $structure['group_by'];

        $data = [];

        foreach ($structure['contribution_type'] as $cotribution) {
            if ($cotribution === 'points') {
                $data = $this->getPointContribution($dates, $structure, $groupBy);
            }

            if ($cotribution === 'problems') {
                $data = $this->getProblemContribution($dates, $structure, $groupBy);
            }


        }


        try {
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

    private function getPointContribution(array $dates, array $structure, string $groupBy)
    {
        $query = Point::query()
            ->select([DB::raw('count(*) as total'), $groupBy])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->groupBy($groupBy);

        if (filled($structure['user_types'])) {
            if (\count($structure['user_types']) !== 2) {
                foreach ($structure['user_types'] as $type) {
                    match ($type) {
                        'user' => $query->whereNotNull('created_by'),
                        'guest' => $query->whereNull('created_by'),
                    };
                }
            }
        }

        $query = $query
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids']))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));

        return $query->get()->pluck('total', $groupBy);
    }

    private function getProblemContribution(array $dates, mixed $structure, mixed $groupBy)
    {
        $query = Problem::query()
            ->select([DB::raw('count(*) as total'), $groupBy])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->groupBy($groupBy);

        if (filled($structure['user_types'])) {
            if (\count($structure['user_types']) !== 2) {
                foreach ($structure['user_types'] as $type) {
                    match ($type) {
                        'user' => $query->whereNotNull('created_by'),
                        'guest' => $query->whereNull('created_by'),
                    };
                }
            }
        }

        $query = $query
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids']))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));

        return $query->get()->pluck('total', $groupBy);
    }
}
