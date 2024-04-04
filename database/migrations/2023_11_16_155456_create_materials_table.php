<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('icon', 191)->nullable();
            $table->integer('order')->nullable()->unique('materials_order_unique');
            $table->unsignedInteger('parent')->nullable();
            $table->boolean('is_wildcard')->nullable()->unique('materials_is_wildcard_unique');
            $table->integer('external_material_id')->nullable();
            $table->string('external_url', 191)->nullable();
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
        Schema::dropIfExists('materials');
    }
}
