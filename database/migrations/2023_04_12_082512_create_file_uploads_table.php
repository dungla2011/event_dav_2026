<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 256);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->string('file_path', 256)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('log')->nullable();
            $table->integer('parent_id')->nullable()->index('parent_id');
            $table->integer('cloud_id')->nullable()->index('cloud_id');
            $table->string('md5', 32)->nullable();
            $table->integer('crc32')->nullable();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_uploads');
    }
}
