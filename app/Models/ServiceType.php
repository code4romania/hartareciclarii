<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $casts = [
        'has_dedicated_issues_tab' => 'boolean',
        'can_have_business_name' => 'boolean',
        'can_offer_money' => 'boolean',
        'can_offer_vouchers' => 'boolean',
        'can_offer_transport' => 'boolean',
        'can_request_payment' => 'boolean',
    ];

    public function issueTypes(): HasMany
    {
        return $this->hasMany(IssueType::class, 'service_type_id');
    }

    public function pointTypes(): HasMany
    {
        return $this->hasMany(PointType::class, 'service_type_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class, 'service_type_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'service_type_id');
    }
}
