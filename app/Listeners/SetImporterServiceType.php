<?php

declare(strict_types=1);

namespace App\Listeners;

use Filament\Actions\Imports\Events\ImportStarted;

class SetImporterServiceType
{
    /**
     * Handle the event.
     */
    public function handle(ImportStarted $event): void
    {
        $event->getImport()->update([
            'service_type_id' => data_get($event->getOptions(), 'service_type_id'),
        ]);
    }
}
