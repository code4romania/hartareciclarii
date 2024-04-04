<?php

declare(strict_types=1);

/**
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-27 11:21:34
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-27 11:46:36
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ReportsExport implements FromCollection
{
    public function __construct(public $data, public array $columns)
    {
    }

    public function collection()
    {
        $columns = $this->formatColumns();
        $exportData[] = $columns;
        $values = [];
        foreach($columns as $column) {
            $record = $this->data->where('grouped_by', $column)->first();
            $value = (string) 0;
            if($record):
                $value = $record->total;
            endif;
            $values[] = $value;
        }
        $exportData[] = $values;

        return collect($exportData);
    }

    public function formatColumns()
    {
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column->getLabel();
        }

        return $columns;
    }
}
