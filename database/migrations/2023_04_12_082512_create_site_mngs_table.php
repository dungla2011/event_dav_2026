<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteMngsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_mngs', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('domain1')->nullable();
            $table->string('domain2')->nullable();
            $table->string('domain3')->nullable();
            $table->string('domain4')->nullable();
            $table->string('domain5')->nullable();
            $table->string('domain6')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_mngs');
    }
}
