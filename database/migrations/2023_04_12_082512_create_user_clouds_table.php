<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCloudsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_clouds', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->index('user_id');
            $table->bigInteger('quota_size')->nullable();
            $table->integer('quota_file')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->nullable();
            $table->string('location_store_file', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_clouds');
    }
}
