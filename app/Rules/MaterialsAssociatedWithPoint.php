<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

class MaterialsAssociatedWithPoint implements ValidationRule
{
    protected Collection $materialIds;

    public function __construct(Collection $materialIds)
    {
        $this->materialIds = $materialIds;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allMaterialsAssociatedWithPoint = collect($value)
            ->every(fn ($materialId) => $this->materialIds->contains($materialId));

        if (! $allMaterialsAssociatedWithPoint) {
            $fail("The $attribute contains materials that are not currently associated with the point.");
        }
    }
}
