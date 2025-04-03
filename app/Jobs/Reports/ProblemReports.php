<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\Point\Status;
use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\City;
use App\Models\County;
use App\Models\PointType;
use App\Models\Problem\Problem;
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
        $group_by = $structure['group_by'];

        $points = Problem::query()
            ->addSelect(DB::raw('count(*) as total'))
            ->addSelect($group_by)
            ->when(filled($dates['start_date']), fn (Builder $query) => $query->whereDate('created_at', '>=', $dates['start_date']))
            ->when(filled($dates['end_date']), fn (Builder $query) => $query->whereDate('created_at', '<=', $dates['end_date']))
            ->when(filled($structure['type_ids']), fn (Builder $query) => $query->whereIn('type_id', $structure['service_ids']))
            ->when(filled($structure['city_ids']), fn (Builder $query) => $query->whereIn('city_id', $structure['city_ids']))
            ->when(filled($structure['county_ids']), fn (Builder $query) => $query->whereIn('county_id', $structure['county_ids']))
            ->groupBy($group_by)
            ->when(filled($structure['status']), fn (Builder $query) => $query->whereIn('status', $structure['status']));

        $points = $points->get()->pluck('total', $group_by);

        $names = match ($group_by) {
            'service_type_id' => ServiceType::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'point_type_id' => PointType::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'city_id' => City::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'county_id' => County::query()->whereIn('id', $points->keys())->pluck('name', 'id'),
            'status' => Status::options(),
        };
        $data = [];

        foreach ($points as $id => $value) {
            $data[$names[$id]] = $value;
        }

        $key = match ($group_by) {
            'service_type_id' => __('report.column.service_type'),
            'point_type_id' => __('report.column.point_type'),
            'city_id' => __('report.column.city'),
            'county_id' => __('report.column.county'),
            'status' => __('report.column.status'),
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
