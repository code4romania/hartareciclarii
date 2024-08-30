<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('has_dedicated_issues_tab')->default(false);
            $table->boolean('can_have_business_name')->default(false);
            $table->boolean('can_offer_money')->default(false);
            $table->boolean('can_offer_vouchers')->default(false);
            $table->boolean('can_offer_transport')->default(false);
            $table->boolean('can_request_payment')->default(false);
            $table->timestamps();
        });
    }
};
