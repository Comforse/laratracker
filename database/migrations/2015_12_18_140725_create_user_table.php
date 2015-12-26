<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string("username", 20);
            $table->string("email", 80);
            $table->char("password_hash", 60);
            $table->char("secret", 20);
            $table->timestamp("created_at");
            $table->timestamp("updated_at");
            $table->timestamp("last_login");
            $table->timestamp("last_seen");
            $table->tinyInteger("account_status", false, true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}
