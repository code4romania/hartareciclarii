<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedPointIssueTypeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_point_issue_type_items', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('reported_point_issue_type_id')->default(0)->index('reported_point_issue_type_id');
            $table->string('title', 250);
            $table->string('step', 250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reported_point_issue_type_items');
    }
}
