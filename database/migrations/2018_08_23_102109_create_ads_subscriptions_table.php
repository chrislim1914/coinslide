<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_subscriptions', function (Blueprint $table) {
            $table->increments('idsubscription');
            $table->integer('iduser');
            $table->integer('idadvertise');
            $table->dateTime('startdate');
            $table->dateTime('enddate');
            $table->tinyInteger('use');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_subscriptions');
    }
}
