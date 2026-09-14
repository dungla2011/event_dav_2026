<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDemoAndTagTblsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demo_and_tag_tbls', function (Blueprint $table) {
            $table->foreign(['demo_id'], 'tag_id_and_demo_id_demo_id_foreign')->references(['id'])->on('demo_tbls')->onDelete('CASCADE');
            $table->foreign(['tag_id'], 'tag_id_and_demo_id_tag_id_foreign')->references(['id'])->on('tag_demos')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demo_and_tag_tbls', function (Blueprint $table) {
            $table->dropForeign('tag_id_and_demo_id_demo_id_foreign');
            $table->dropForeign('tag_id_and_demo_id_tag_id_foreign');
        });
    }
}
