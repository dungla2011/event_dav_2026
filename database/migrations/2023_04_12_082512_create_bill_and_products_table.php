<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillAndProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_and_products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->integer('bill_id')->nullable();
            $table->integer('sku_id')->nullable();
            $table->string('sku_string', 256)->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('price')->nullable();
            $table->integer('price_org')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('client_session_time', 16)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_and_products');
    }
}
