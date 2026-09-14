<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkusProductVariantOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skus_product_variant_options', function (Blueprint $table) {
            $table->integer('sku_id');
            $table->integer('product_variant_id')->index('spvo_product_variant_id_product_var_idx');
            $table->integer('product_variant_options_id')->index('spvo_product_variant_options_id_pro_idx');
            $table->integer('id', true)->index('id1');
            $table->softDeletes();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->unique(['sku_id', 'product_variant_id'], 'UNIQUE_sku_id_product_variant_id');
            $table->primary(['sku_id', 'product_variant_options_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skus_product_variant_options');
    }
}
