<?php

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
            $table->bigIncrements('id');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->unique('email');
            $table->bigInteger('phone_number')->nullable();
            $table->timestamps();
            $table->integer('is_admin')->nullable()->default(0);
            $table->softDeletes();
            $table->string('token_user')->nullable()->index('token_user');
            $table->integer('site_id')->nullable()->default(0);
            $table->string('name', 128)->nullable();
            $table->rememberToken();
            $table->timestamp('email_active_at')->nullable();
            $table->string('reg_str', 256)->nullable()->index('reg_str');
            $table->text('log')->nullable();
            $table->string('reset_pw', 128)->nullable()->index('reset_pw');
            $table->string('avatar', 128)->nullable();
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
