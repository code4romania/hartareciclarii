<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldTypeRecyclingPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_type_recycling_point', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_type_id');
            $table->unsignedInteger('recycling_point_id')->index('recycling_point_idx');
            $table->text('value')->nullable();
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('field_type_id', 'field_type_recycling_point_field_type_id_foreign')->references('id')->on('field_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_type_recycling_point');
    }
}
