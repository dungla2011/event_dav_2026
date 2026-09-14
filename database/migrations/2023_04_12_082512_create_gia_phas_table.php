<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiaPhasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gia_phas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('parent_id')->nullable()->default(0)->index('parent_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('name', 256);
            $table->string('title', 64)->nullable();
            $table->string('home_address', 64)->nullable();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->integer('orders')->nullable()->default(0)->index('orders');
            $table->smallInteger('child_type')->nullable()->comment('=1 là con dâu, rể');
            $table->smallInteger('gender')->nullable()->default(1);
            $table->string('birthday', 30)->nullable();
            $table->string('date_of_death', 32)->nullable();
            $table->string('place_birthday', 64)->nullable();
            $table->string('place_heaven', 64)->nullable();
            $table->integer('child_of_second_married')->nullable()->index('child_of_second_married');
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('last_name', 128)->nullable();
            $table->string('sur_name', 128)->nullable();
            $table->integer('married_with')->nullable()->index('married_with');
            $table->string('image_list', 1024)->nullable();
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('tmp_old_id')->nullable()->index('tmp_old_id');
            $table->integer('tmp_old_pid')->nullable();
            $table->text('tmp_old_obj_json')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('email_address', 64)->nullable();
            $table->mediumInteger('col_fix')->nullable();
            $table->mediumInteger('row_fix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gia_phas');
    }
}
