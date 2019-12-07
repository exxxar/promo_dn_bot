<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('user_has_achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('activated')->default(false);
            $table->unsignedInteger("user_id" );
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger("achievement_id" );
            $table->foreign('achievement_id')->references('id')->on('achievements');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_achievements');
    }
}
