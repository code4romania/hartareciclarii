<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecyclingPointTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycling_point_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('service_id')->default(0);
            $table->string('type_name', 64);
            $table->string('display_name', 128);
            $table->string('icon', 128)->nullable();
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
        Schema::dropIfExists('recycling_point_types');
    }
}
