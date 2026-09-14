<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_folders', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 256)->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->text('log')->nullable();
            $table->smallInteger('status')->nullable();
            $table->integer('orders')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_folders');
    }
}
