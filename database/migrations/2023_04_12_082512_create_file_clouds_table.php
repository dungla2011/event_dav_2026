<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileCloudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_clouds', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 512);
            $table->bigInteger('size')->nullable();
            $table->string('file_path', 256)->nullable();
            $table->string('md5', 32)->nullable();
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('crc32')->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('location', 256)->nullable();
            $table->string('mime', 64)->nullable();
            $table->string('server1', 128)->nullable();
            $table->string('location1', 128)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_clouds');
    }
}
