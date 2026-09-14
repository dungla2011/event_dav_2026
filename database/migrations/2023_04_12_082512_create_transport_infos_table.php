<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_infos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 256)->nullable()->comment('Tên chuyến');
            $table->string('from_address', 256)->nullable()->comment('Đi từ');
            $table->string('to_address', 256)->nullable()->comment('Đi đến');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('user_id')->nullable()->default(0)->comment('uid tạo đơn này');
            $table->string('phone_request', 30)->nullable()->comment('phone khách nếu có');
            $table->string('email_request', 50)->nullable()->comment('email khách nếu có');
            $table->text('text_desc')->nullable()->comment('Mô tả text , copy từ chat..., voice');
            $table->integer('user_id_post')->nullable()->comment('uid yêu cầu chuyến nếu có');
            $table->integer('user_id_get')->nullable()->comment('uid nhận chuyến nếu có');
            $table->integer('service_require')->nullable()->comment('Loại dịch vụ: xe 4, 6 chỗ');
            $table->timestamp('start_time')->nullable()->comment('Thời gian bắt đầu cần dịch vụ');
            $table->timestamp('end_time')->nullable();
            $table->integer('money')->nullable()->comment('Số tiền');
            $table->timestamp('done_at')->nullable()->comment('Thành công');
            $table->smallInteger('status')->nullable()->comment('Trạng thái: thành công, hủy...');
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
        Schema::dropIfExists('transport_infos');
    }
}
