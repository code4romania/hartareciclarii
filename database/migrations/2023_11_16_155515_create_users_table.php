<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 191);
            $table->string('lastname', 191);
            $table->string('email', 191)->nullable()->unique('users_hr_email_unique');
            $table->string('password', 191)->default('#####');
            $table->boolean('accept_terms')->default(0);
            $table->boolean('send_newsletter')->default(0);
            $table->rememberToken();
            $table->unsignedInteger('email_confirmed')->default(1);
            $table->unsignedInteger('last_login_ip')->default(1);
            $table->timestamp('last_login_date')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->string('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
