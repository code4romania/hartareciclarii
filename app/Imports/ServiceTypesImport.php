<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceTypesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        ServiceType::insert(
            $collection
                ->map(fn (Collection $row) => [
                    'name' => $row->get('name'),
                    'slug' => $row->get('slug'),
                    'has_dedicated_issues_tab' => (bool) $row->get('has_dedicated_issues_tab'),
                    'can_have_business_name' => (bool) $row->get('can_have_business_name'),
                    'can_offer_money' => (bool) $row->get('can_offer_money'),
                    'can_offer_vouchers' => (bool) $row->get('can_offer_vouchers'),
                    'can_offer_transport' => (bool) $row->get('can_offer_transport'),
                    'can_request_payment' => (bool) $row->get('can_request_payment'),
                    'can_collect_materials' => (bool) $row->get('can_collect_materials'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all()
        );
    }
}
