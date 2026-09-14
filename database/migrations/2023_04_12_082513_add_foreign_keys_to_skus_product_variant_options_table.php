<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSkusProductVariantOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skus_product_variant_options', function (Blueprint $table) {
            $table->foreign(['sku_id'], 'skus_product_variant_options_sku_id_skus_id')->references(['id'])->on('skus')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['product_variant_options_id'], 'spvo_product_variant_options_id_product_variant_options_id')->references(['id'])->on('product_variant_options')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['product_variant_id'], 'spvo_product_variant_id_product_variants_id')->references(['id'])->on('product_variants')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skus_product_variant_options', function (Blueprint $table) {
            $table->dropForeign('skus_product_variant_options_sku_id_skus_id');
            $table->dropForeign('spvo_product_variant_options_id_product_variant_options_id');
            $table->dropForeign('spvo_product_variant_id_product_variants_id');
        });
    }
}
