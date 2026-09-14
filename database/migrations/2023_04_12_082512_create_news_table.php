<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 256);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->timestamp('created_at')->useCurrent()->index('created_at');
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->text('log')->nullable();
            $table->integer('parent_id')->nullable()->index('parent_id');
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->string('image_list', 256)->nullable();
            $table->smallInteger('status')->nullable()->default(0)->index('status');
            $table->integer('options')->nullable()->index('options')->comment('1 kiểu phân loại, ví dụ =1, ở top trang chủ; = 2 ở slide giữa...
');
            $table->integer('orders')->nullable()->index('orders');
            $table->integer('publish_status')->nullable();
            $table->integer('count_view')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
