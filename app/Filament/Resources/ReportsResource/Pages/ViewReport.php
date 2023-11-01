<?php

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Filament\Resources\ReportsResource;
use App\Models\MapPointService as MapPointServiceModel;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportsResource::class;

    protected static string $view = 'filament.resources.reports-resource.pages.view-report';

    public $lat;

    public $lon;

    public $city;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function getSubHeading(): string | Htmlable
    {
        $filters = $this->getRecord()->form_data;
        $text = '';

        foreach($filters['filters'] as $filter => $value):
            if(!empty($value)):
                if($filter == 'range' && (!empty($value[0]) || !empty($value[1]))):
                    $text .= __('report.column.' . $filter) . ': ' . implode(',', $value) . '<br />';
                elseif($filter != 'range'):
                    $text .= $this->formatFilter($filter, $value);
                endif;
            endif;
        endforeach;
        $text .= __('report.labels.grouped_by') . __('report.column.' . $filters['group']) . '<br />';
        $text .= __('report.labels.generated_at') . $this->getRecord()->created_at;

        return new HtmlString($text);
    }

    public function formatFilter($filter, $value)
    {
        switch($filter)
        {
            case 'service_type':
                $value = MapPointServiceModel::whereIn('id', $value)->get()->pluck('display_name')->toArray();
                break;
        }

        return __('report.column.' . $filter) . ': ' . implode(',', $value) . '<br />';
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        return $actions;
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();

        $title = '#' . $record->id . ' ' . $record->title;

        return new HtmlString($title);
    }
}
