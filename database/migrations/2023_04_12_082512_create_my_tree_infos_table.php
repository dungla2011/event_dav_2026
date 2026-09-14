<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyTreeInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_tree_infos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 256);
            $table->string('title', 256)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('tree_id')->nullable()->unique('tree_id');
            $table->tinyInteger('status')->nullable();
            $table->string('image_list', 64)->nullable();
            $table->string('color_name', 12)->nullable();
            $table->string('color_title', 12)->nullable();
            $table->smallInteger('fontsize_name')->nullable();
            $table->smallInteger('fontsize_title')->nullable();
            $table->smallInteger('banner_name_margin_top')->nullable()->default(0);
            $table->smallInteger('banner_name_margin_bottom')->nullable()->default(0);
            $table->smallInteger('banner_title_margin_top')->nullable()->default(0);
            $table->smallInteger('banner_title_margin_bottom')->nullable()->default(0);
            $table->string('member_background_img', 256)->nullable();
            $table->string('member_background_img2', 256)->nullable();
            $table->mediumInteger('banner_width')->nullable();
            $table->mediumInteger('banner_height')->nullable();
            $table->string('banner_name_bold', 20)->nullable();
            $table->string('banner_name_italic', 20)->nullable();
            $table->string('banner_title_bold', 20)->nullable();
            $table->string('banner_title_italic', 20)->nullable();
            $table->mediumInteger('banner_title_curver')->nullable();
            $table->mediumInteger('banner_name_curver')->nullable();
            $table->string('banner_text_shadow_name', 30)->nullable();
            $table->string('banner_text_shadow_title', 30)->nullable();
            $table->smallInteger('banner_margin_top')->nullable();
            $table->tinyInteger('title_before_or_after_name')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_tree_infos');
    }
}
