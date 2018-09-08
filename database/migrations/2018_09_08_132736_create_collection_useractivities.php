<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionUseractivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->create('useractivities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iduser');
            $table->string('date');
            $table->string('idads');
            $table->string('idsubscription');
            $table->string('activity');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('useractivities');
    }
}
