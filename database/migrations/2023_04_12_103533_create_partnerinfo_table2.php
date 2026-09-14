<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerinfoTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_infos', function (Blueprint $table) {
            $table->integer('id', true)->change();
            $table->string('name', 128)->nullable()->comment('Tên thông tin');
            $table->softDeletes();
            $table->string('partner_name', 64)->nullable()->default(0)->comment('Tên parnet');
            $table->string('token_api')->nullable()->comment('Số tiền');
            $table->text('note')->nullable()->comment('Mô tả');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partnerinfo_table2');
    }
}
