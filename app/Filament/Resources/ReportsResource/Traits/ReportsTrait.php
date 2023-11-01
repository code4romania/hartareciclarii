<?php

/*
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-31 11:05:30
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-31 13:19:59
 */

namespace App\Filament\Resources\ReportsResource\Traits;

// use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ReportsTrait
{
    use MapPointsReportTrait;

    use IssuesReportTrait;

    public $isGrouped = false;

    public $groupedBy = null;

    public $shouldGenerate = false;

    public function updateGoupedBy($field)
    {
        $this->groupedBy = $field;
        $this->shouldGenerate = false;
    }

    public function form(Form $form): Form
    {
        $method = 'get' . ucfirst(Str::camel($this->type)) . 'Form';

        return $this->$method($form);
    }

    public function getEloquentQuery(): Builder
    {
        $this->data['type'] = $this->data['type'] ? $this->data['type'] : 'map_points';
        $this->type = $this->data['type'];
        switch($this->type)
        {
            case 'map_points':
                return $this->getMapPointsEloquentQuery();
                break;
        }
    }

    public function getReportTableColumns(): array
    {
        $this->data['type'] = $this->data['type'] ? $this->data['type'] : 'map_points';
        $this->type = $this->data['type'];
        $method = 'get' . ucfirst(Str::camel($this->type)) . 'TableColumns';

        return $this->$method();
    }

    public function getReportTable(Table $table): Table
    {
        $this->data['type'] = $this->data['type'] ? $this->data['type'] : 'map_points';
        $this->type = $this->data['type'];
        $method = 'get' . ucfirst(Str::camel($this->type)) . 'Table';

        return $this->$method($table);
    }

    public function formatResults(Collection $items): Collection
    {
        $values = [];
        $columns = [];
        foreach ($this->getReportTableColumns() as $column)
        {
            $columns[] = $column->getLabel();
        }
        $returnArr['header'] = $columns;
        foreach($columns as $column)
        {
            $record = $items->where('grouped_by', $column)->first();
            $value = 0;
            if($record):
                $value = $record->total;
            endif;
            $values[] = $value;
        }
        $returnArr['results'] = $values;

        return collect($returnArr);
    }
}
