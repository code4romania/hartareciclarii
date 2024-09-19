<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

class MaterialsMissingFromPoint implements ValidationRule
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
        $alreadyHasMaterials = collect($value)
            ->intersect($this->materialIds)
            ->isNotEmpty();

        if ($alreadyHasMaterials) {
            $fail("The $attribute contains materials that are already associated with the point.");
        }
    }
}
