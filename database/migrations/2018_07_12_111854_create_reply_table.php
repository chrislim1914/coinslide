<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('replies', function (Blueprint $table) {
            $table->increments('idreply');
            $table->integer('idcomment');
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
        Schema::dropIfExists('replies');
    }
}
