<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecyclePointDuplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycle_point_duplicates', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('recycle_point_1')->nullable();
            $table->integer('recycle_point_2')->nullable();
            $table->float('distance')->nullable();
            $table->integer('point_type_id')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recycle_point_duplicates');
    }
}
