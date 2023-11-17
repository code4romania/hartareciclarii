<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecyclingPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycling_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lat', 20)->index('recycling_points_lat_index');
            $table->string('lon', 20)->index('recycling_points_lon_index');
            $table->point('location');
            $table->integer('rating_1')->default(0);
            $table->integer('rating_2')->default(0);
            $table->integer('rating_3')->default(0);
            $table->integer('rating_4')->default(0);
            $table->integer('rating_5')->default(1);
            $table->unsignedInteger('point_type_id')->index('point_type_idx');
            $table->enum('point_source', ['import', 'user']);
            $table->integer('group_id')->nullable();
            $table->integer('status')->default(0)->comment("0 - new, 1 - active");
            $table->integer('service_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recycling_points');
    }
}
