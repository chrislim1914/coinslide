<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('subscriptions', function (Blueprint $table) {
            $table->increments('idsubscription');
            $table->integer('iduser');
            $table->integer('idadvertiser');
            $table->timestamp('startdate');
            $table->timestamp('enddate');
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
        Schema::dropIfExists('subscriptions');
    }
}
