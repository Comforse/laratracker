<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePeerTorrentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peer_torrent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('peer_id', false, true)->references('id')->on('peer');
            $table->integer('torrent_id', false, true)->references('id')->on('torrent');
            $table->integer('uploaded', false, true)->default(0);
            $table->integer('downloaded', false, true)->default(0);
            $table->integer('left', false, true)->default(0);
            $table->boolean('stopped')->default(false);
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
        Schema::drop('peer_torrent');
    }
}
