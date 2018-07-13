<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserinformationCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->create('userinformations', function (Blueprint $collection) {
            $collection->increments('id');
            $collection->integer('iduser');
            $collection->string('gender');
            $collection->string('profilephoto');
            $collection->string('birthdate');
            $collection->string('city');
            $collection->string('maritalstatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userinformations');
    }
}
