<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_trees', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->integer('parent_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('orders')->nullable()->default(0);
            $table->string('link', 512)->nullable()->default('');
            $table->string('gid_allow')->nullable();
            $table->tinyInteger('open_new_window')->nullable()->default(0);
            $table->string('icon', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_trees');
    }
}
