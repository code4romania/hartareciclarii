<?php

declare(strict_types=1);

namespace App\Jobs\Reports;

use App\Enums\ReportStatus;
use App\Filament\Resources\ReportResource;
use App\Models\Point;
use App\Models\Problem\Problem;
use App\Models\Report;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
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

        $query = DB::table('contributions')
            ->select([DB::raw('count(*) as total'), 'user_id'])
            ->whereDate('created_at', '>=', $dates['start_date'])
            ->whereDate('created_at', '<=', $dates['end_date'])
            ->when($structure['contribution_type'] === 'points', fn (Builder $q) => $q->where('model_type', 'place'))
            ->when($structure['contribution_type'] === 'problems', fn (Builder $q) => $q->where('model_type', 'problem'))
            ->groupBy('user_id');

//        $query = $query
//            ->when($structure['city_ids'],  (Builder $q) => $q->('city_id', $structure['city_ids']))
//            ->when($structure['county_ids'], fn (Builder $q) => $q->whereIn('county_id', $structure['county_ids']));
//

        $data = $query->get()->pluck('total', 'user_id');

        try {
            $tmpData = [];
            $names = User::whereIn('id', $data->keys())->get()->pluck('name', 'id');
            foreach ($data as $key => $value) {
                if (empty($key)) {
                    $key = 'N/A';
                }else
                {
                    $key= $names[$key] ?? 'N/A';
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

}
