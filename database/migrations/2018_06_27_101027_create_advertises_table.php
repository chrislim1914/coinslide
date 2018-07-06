<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->increments('idadvertise');
            $table->integer('idadvertisers');
            $table->string('adcategory');
            $table->string('title');
            $table->string('content');
            $table->string('url');
            $table->string('img');
            $table->timestampTz('createdate');
            $table->timestamp('startdate');
            $table->timestamp('enddate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertises');
    }
}
