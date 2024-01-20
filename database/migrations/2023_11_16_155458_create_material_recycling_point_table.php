<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialRecyclingPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_recycling_point', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('material_id');
            $table->unsignedInteger('recycling_point_id')->index('recycling_point_idx');
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamps();
            
            $table->foreign('material_id', 'material_recycling_point_material_id_foreign')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_recycling_point');
    }
}
