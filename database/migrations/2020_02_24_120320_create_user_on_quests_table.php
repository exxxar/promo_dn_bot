<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOnQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_on_quests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('geo_quest_id');
            $table->unsignedInteger('user_id');

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('geo_quest_id')->references('id')->on('geo_quests');
                $table->foreign('user_id')->references('id')->on('users');
            }
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
        Schema::dropIfExists('user_on_quests');
    }
}
