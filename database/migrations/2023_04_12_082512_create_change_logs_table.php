<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('user_id_admin')->nullable()->index('user_id_admin');
            $table->mediumText('change_log')->nullable();
            $table->string('tables', 128)->nullable()->index('tables');
            $table->integer('id_row')->nullable()->index('id_row');
            $table->string('cmd', 24)->nullable();
            $table->string('ip_address', 32)->nullable();
            $table->text('tag_log')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_logs');
    }
}
