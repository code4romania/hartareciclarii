<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Page;

use App\Services\MapStatisticsExporter;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportMapStatistics extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export_map_statistics';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-chart-bar');
        $this->label(__('map_points.buttons.export_statistics'));
        $this->color('gray');
        $this->successNotification(null);
        $this->action(function (MapStatisticsExporter $exporter): BinaryFileResponse {
            set_time_limit(0);

            $path = $exporter->exportToZip();

            return response()->download(
                $path,
                basename($path),
                ['Content-Type' => 'application/zip']
            )->deleteFileAfterSend(true);
        });
    }
}
