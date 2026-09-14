<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->index('skus_product_id_products_id_idx');
            $table->string('sku', 45)->nullable();
            $table->integer('price0')->nullable();
            $table->integer('price')->nullable();
            $table->integer('weight')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->string('product_opt_list', 256)->nullable()->fulltext('product_opt_list');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skus');
    }
}
