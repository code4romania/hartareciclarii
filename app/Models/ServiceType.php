<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Problem\ProblemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'has_dedicated_issues_tab',
        'can_have_business_name',
        'can_offer_money',
        'can_offer_vouchers',
        'can_offer_transport',
        'can_request_payment',
        'can_collect_materials',
    ];

    protected $casts = [
        'has_dedicated_issues_tab' => 'boolean',
        'can_have_business_name' => 'boolean',
        'can_offer_money' => 'boolean',
        'can_offer_vouchers' => 'boolean',
        'can_offer_transport' => 'boolean',
        'can_request_payment' => 'boolean',
        'can_collect_materials' => 'boolean',
    ];

    public function pointTypes(): HasMany
    {
        return $this->hasMany(PointType::class, 'service_type_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class, 'service_type_id');
    }

    public function problemTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProblemType::class);
    }
}
