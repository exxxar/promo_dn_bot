<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('geo_quest_id');
            $table->unsignedInteger('geo_position_id');
            $table->unsignedInteger('user_id');

            if (env("DB_CONNECTION")=='mysql') {
                $table->foreign('geo_quest_id')->references('id')->on('geo_quests');
                $table->foreign('geo_position_id')->references('id')->on('geo_positions');
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
        Schema::dropIfExists('geo_histories');
    }
}
