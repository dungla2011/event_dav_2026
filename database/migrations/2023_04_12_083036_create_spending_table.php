<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpendingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spendings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 256)->nullable()->comment('Tên spend');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('user_id')->nullable()->default(0)->comment('uid tạo spen này');
            $table->integer('cat')->nullable()->comment('Phân loại');
            $table->integer('money')->nullable()->comment('Số tiền');
            $table->text('note')->nullable()->comment('Mô tả spend');
            $table->string('image_list', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spendings');
    }
}
