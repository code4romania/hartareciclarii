<?php

namespace App\Console\Commands;

use App\Models\ImportExport as ImportExportModel;
use App\Models\MapPoint as MapPointModel;
use Illuminate\Console\Command;

class ProcessImports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harta:process-imports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process xlsx imports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Started');
        $result = [
            'failed'=>[],
            'processed'=>[],
            'errors' => [],

        ];
        $items = ImportExportModel::whereStatus(0)->orderBy('created_at', 'asc')->get();

        if ($items->isNotEmpty())
        {
            foreach ($items as $item)
            {
                $filePath = storage_path('app/public/' . $item->file);
                // $item->status = 1;
                $item->started_at = date('Y-m-d H:i:s');
                $item->save();
                if (!file_exists($filePath))
                {
                    $item->result =
                        [
                            'processed'=>[],
                            'failed'=>[],
                            'errors'=>[
                                'file_not_found',
                            ],
                        ];
                    $item->status = 2;
                    $item->finished_at = date('Y-m-d H:i:s');
                    $item->save();
                }
            }

            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for ($row = 2; $row <= $highestRow; $row++)
            {
                $mapPoint = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                if (!empty($mapPoint))
                {
                    if (!empty(array_filter($mapPoint[0], fn ($item) => !\is_null($item))))
                    {
                        $errors = [];
                        $data = [];
                        $isValid = ImportExportModel::validateAndMapImportField(collect($mapPoint[0]), $errors, $data);
                        if (\count($errors))
                        {
                            $result['failed']['A' . $row] = $errors;
                        }
                        else
                        {
                            $data['created_by'] = 0;
                            $data['point_source'] = 'import';
                            $record = MapPointModel::createFromArray($data);
                            $result['processed']['A' . $row] = $record->id;
                        }
                    }
                }
                else
                {
                    $result['failed']['A' . $row] = ['not_data_found'];
                }
            }
        }
        $item->status = 2;
        $item->result = $result;
        $item->finished_at = date('Y-m-d H:i:s');
        $item->save();
        $this->info('Done. Failed: ' . \count($result['failed']) . '. Processed: ' . \count($result['processed']) . '. Errors: ' . \count($result['errors']));
    }
}
