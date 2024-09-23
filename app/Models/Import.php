<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Actions\Imports\Models\Import as FilamentImportModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends FilamentImportModel
{
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class);
    }

    public function getFillable()
    {
        $parentFillable = parent::getFillable();
        $parentFillable[] = 'service_type_id';

        return $parentFillable;
    }
}
