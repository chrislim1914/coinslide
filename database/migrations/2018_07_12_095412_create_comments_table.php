<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('comments', function (Blueprint $table) {
            $table->increments('idcomment');
            $table->integer('idcontent');
            $table->integer('iduser');
            $table->string('content');
            $table->timestamp('createdate');
            $table->timestamp('modifieddate');
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
        Schema::dropIfExists('comments');
    }
}
