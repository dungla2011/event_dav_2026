<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_ships', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('fee')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('user_id')->nullable()->index('user_id');
            $table->integer('vendor_id')->nullable()->index('vendor_id');
            $table->integer('order_id')->index('order_id');
            $table->string('remote_tracking_id', 30)->nullable()->index('remote_id');
            $table->integer('status')->nullable()->index('status');
            $table->text('log')->nullable();
            $table->timestamp('pick_time')->nullable();
            $table->timestamp('delive_time')->nullable();
            $table->string('remote_label', 64)->nullable();
            $table->text('json_send')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_ships');
    }
}
