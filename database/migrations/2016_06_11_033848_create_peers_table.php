<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash', 40)->nullable();
            $table->string('user_agent', 80);
            $table->string('ip_address', 16);
            $table->string('passkey', 32);
            $table->integer('port', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('peer');
    }
}
