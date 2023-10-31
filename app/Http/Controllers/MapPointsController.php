<?php

/*
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-03 20:27:58
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-10-10 10:34:59
 */

namespace App\Http\Controllers;

use App\Models\ImportExport as ImportExportModel;
use App\Models\MapPoint as MapPointModel;
use Filament\Notifications\Notification;

class MapPointsController extends Controller
{
    public function validatePoint($point_id)
    {
        if (!auth()->user()->can('manage_map_points'))
        {
            return redirect(\URL::previous());
        }
        $point = MapPointModel::find($point_id);
        if (!$point)
        {
            Notification::make()
                ->title('Point not found')
                ->danger()
                ->send();

            return redirect(\URL::previous());
        }
        $point->status = 1;
        $point->save();
        Notification::make()
            ->title('Point saved successfully')
            ->success()
            ->send();

        return redirect(\URL::previous());
    }

    public function downloadXlsxExample()
    {
        $xls = ImportExportModel::downloadMapPointSample();
        header('Content-type:application/xlsx');
        header("Content-Disposition:attachment;filename={$xls['filename']}");
        echo base64_decode($xls['content']);
        die();
    }
}
