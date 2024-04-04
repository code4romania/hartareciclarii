<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedPointIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_point_issues', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('point_id');
            $table->float('latitude', 10, 8)->default(0.00000000);
            $table->float('longitude', 10, 8)->default(0.00000000);
            $table->integer('reporter_id')->nullable();
            $table->integer('status')->nullable();
            $table->integer('reported_point_issue_type_id')->nullable()->index('reported_point_issue_type_id');
            $table->json('material_issue')->nullable();
            $table->json('material_issue_missing')->nullable();
            $table->json('material_issue_extra')->nullable();
            $table->json('collection_decline_reason')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reported_point_issues');
    }
}
