<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertiserBanner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('advertiser_banners', function (Blueprint $table) {
            $table->increments('idadvertiser_banner');
            $table->integer('idadvertiser');
            $table->string('img');
            $table->integer('position');
            $table->integer('use');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertiser_banners');
    }
}
