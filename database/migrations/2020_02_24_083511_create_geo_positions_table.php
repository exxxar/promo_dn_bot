<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->comment('Краткое название локации');
            $table->string('description',1000)->default('')->comment('Краткое описание локационного задания');
            $table->string('image_url',1000)->default('')->comment('Изображение локации');
            $table->double('latitude')->default(0.0);
            $table->double('longitude')->default(0.0);
            $table->integer('radius')->default(0);
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
        Schema::dropIfExists('geo_positions');
    }
}
