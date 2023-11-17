<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_aliases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent');
            $table->string('alias', 191);
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('created_at')->nullable();
            
            $table->foreign('parent', 'material_aliases_parent_foreign')->references('id')->on('materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_aliases');
    }
}
