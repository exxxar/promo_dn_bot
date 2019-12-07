<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('description',1000)->default('');
            $table->string('ach_image_url',1000)->nullable(false);
            $table->integer('trigger_type')->nullable(false);
            $table->integer('trigger_value')->default(0);
            $table->string('prize_description',1000)->nullable(false);
            $table->string('prize_image_url',1000)->nullable(false);
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
        Schema::dropIfExists('achievements');
    }
}
