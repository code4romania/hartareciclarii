<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\Point;
use App\Models\Problem\Problem;
use App\Models\Report;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class TopUsersReports implements ShouldQueue
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

        if ($structure['contribution_type'] === 'points') {
            $data = $this->getPointContribution($dates, $structure, $groupBy);
        }
        if ($structure['contribution_type'] === 'problems') {
            $data = $this->getProblemContribution($dates, $structure);
        }

        try {
            $tmpData = [];
            foreach ($data as $key => $value) {
                if (empty($key)) {
                    $key = 'N/A';
                }
                $tmpData[$key] = $value;
            }

            $this->report->update([
                'results' => $tmpData,
                'label' => 'Utilizator',
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

    private function getPointContribution(array $dates, array $structure)
    {
        $query = Point::query()
            ->select([DB::raw('count(*) as total'), 'created_by'])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->groupBy('created_by');

        $query = $query
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids']))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));

        return $query->get()->pluck('total', 'created_by');
    }

    private function getProblemContribution(array $dates, mixed $structure)
    {
        $query = Problem::query()
            ->select([DB::raw('count(*) as total'), 'reported_by'])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->groupBy('reported_by');

        $query = $query
            ->when($structure['city_ids'], fn (Builder $q) => $q->whereIn('city_id', $structure['city_ids']))
            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));

        return $query->get()->pluck('total', 'reported_by');
    }
}
