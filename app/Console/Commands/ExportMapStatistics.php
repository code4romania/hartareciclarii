<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MapStatisticsExporter;
use Illuminate\Console\Command;

class ExportMapStatistics extends Command
{
    protected $signature = 'map:export-statistics {path?}';

    protected $description = 'Export all map points and Romania coverage (counties and localities) as a ZIP of CSV files';

    public function handle(MapStatisticsExporter $exporter): int
    {
        $path = $this->argument('path');

        if (\is_string($path) && is_dir($path)) {
            $path = rtrim($path, \DIRECTORY_SEPARATOR) . '/statistica-harta-' . now()->format('Y-m-d-His') . '.zip';
        }

        $this->info('Generating map statistics export...');

        $zipPath = $exporter->exportToZip($path);

        $this->info("Saved to {$zipPath}");

        return self::SUCCESS;
    }
}
