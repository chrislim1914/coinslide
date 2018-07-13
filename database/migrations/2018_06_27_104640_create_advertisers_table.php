<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('advertisers', function (Blueprint $table) {
            $table->increments('idadvertiser');
            $table->integer('iduser');
            $table->string('company_name');
            $table->string('business_registration');
            $table->string('business_category');
            $table->string('representative_name');
            $table->string('representative_contactno');
            $table->string('company_website');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertisers');
    }
}
