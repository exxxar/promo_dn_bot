<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestHasPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest_has_points', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('geo_quest_id');
            $table->unsignedInteger('geo_position_id');
            $table->integer('position')->default(0);
            $table->boolean('is_last')->default(false);

            if (env("DB_CONNECTION")=='mysql') {
                $table->foreign('geo_quest_id')->references('id')->on('geo_quests');
                $table->foreign('geo_position_id')->references('id')->on('geo_positions');
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
        Schema::dropIfExists('quest_has_points');
    }
}
