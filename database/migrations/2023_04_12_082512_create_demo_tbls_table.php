<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemoTblsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_tbls', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->integer('number1')->nullable();
            $table->integer('number2')->nullable();
            $table->string('string1')->nullable();
            $table->string('string2')->nullable();
            $table->string('textarea1')->nullable();
            $table->string('textarea2')->nullable();
            $table->text('tag_list_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('parent2')->nullable();
            $table->text('parent_multi')->nullable();
            $table->text('parent_multi2')->nullable();
            $table->text('image_list1')->nullable();
            $table->text('image_list2')->nullable();
            $table->integer('orders')->nullable()->default(0);
            $table->string('name', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demo_tbls');
    }
}
