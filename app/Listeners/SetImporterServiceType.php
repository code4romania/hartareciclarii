<?php

namespace App\Listeners;

use Filament\Actions\Imports\Events\ImportStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SetImporterServiceType
{
    /**
     * Handle the event.
     */
    public function handle(ImportStarted $event): void
    {
        Log::info($event);
    }
}
