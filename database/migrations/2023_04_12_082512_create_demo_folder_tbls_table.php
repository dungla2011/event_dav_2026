<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemoFolderTblsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_folder_tbls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('parent_id')->nullable()->default('0');
            $table->string('summary')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('log')->nullable();
            $table->integer('orders')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demo_folder_tbls');
    }
}
