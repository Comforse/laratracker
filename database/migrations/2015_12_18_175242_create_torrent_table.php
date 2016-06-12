<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTorrentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('torrent', function (Blueprint $table) {
            $table->increments('id');
            $table->string('string_id', 5);
            $table->string('name', 80);
            $table->text('description');
            $table->string('filename', 80);
            $table->integer('category_id', false, true)->references('id')->on('category');
            $table->mediumText('nfo');
            $table->string('info_hash', 40);
            $table->string('hash', 40);
            $table->string('size', 20)->default('0');
            $table->text('files_list');
            $table->string('picture', 45)->nullable();
            $table->integer('seeders', false, true);
            $table->integer('leechers', false, true);
            $table->boolean('visible')->default(1);
            $table->boolean('nuked')->default(0);
            $table->text('nuked_reason')->nullable();
            $table->integer('views', false, true);
            $table->integer('times_completed', false, true);
            $table->dateTime('last_action')->nullable();
            $table->integer('comments', false, true);
            $table->boolean('comments_enabled')->default(true);
            $table->boolean('free_leech')->default(false);
            $table->integer('user_id', false, true)->references('id')->on('user');
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
        Schema::drop('torrent');
    }
}
