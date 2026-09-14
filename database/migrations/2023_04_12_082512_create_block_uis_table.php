<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockUisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_uis', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->string('sname', 128)->nullable()->unique('sname');
            $table->text('summary')->nullable();
            $table->string('module_table')->nullable();
            $table->string('idModule', 1024)->nullable();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->text('log')->nullable();
            $table->integer('siteid')->nullable();
            $table->text('extra_info')->nullable();
            $table->string('image_list', 1024)->nullable();
            $table->string('tags_list', 1024)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->smallInteger('status')->nullable()->default(1);
            $table->text('content')->nullable();
            $table->text('guide_admin')->nullable();
            $table->string('extra_color_background', 10)->nullable();
            $table->string('extra_color_text', 10)->nullable();
            $table->string('group_name', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_uis');
    }
}
