<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('field_name', 256);
            $table->string('display_name', 512);
            $table->boolean('display_to_user')->default(1);
            $table->string('icon', 191)->nullable();
            $table->enum('field_type', ['detail_text', 'navigation_link', 'boolean_value', 'web_link', 'tel_link', 'email_link', 'schedule'])->default('detail_text');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_types');
    }
}
