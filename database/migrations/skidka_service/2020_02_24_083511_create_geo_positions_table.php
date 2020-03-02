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
            $table->string('description', 1000)->default('')->comment('Краткое описание локационного задания');
            $table->string('image_url', 1000)->default('')->comment('Изображение локации');
            $table->double('latitude')->default(0.0)->comment("Широта");
            $table->double('longitude')->default(0.0)->comment("Долгота");
            $table->double('radius')->default(0)->comment("Радиус активации точки");

            $table->integer('local_promotion_id')->nullable()->comment("Можно добавить локальную акционную плюшку");

            $table->integer('local_reward')->default(0)->comment("Бонус при посещении точки квеста (опционально)");

            $table->integer('in_time_range')->default(false)->comment("Активировать сразу или по истечению времени");
            $table->integer('range_time_value')->default(0)->comment('Время ожидания на точке');

            $table->time('time_start')->default(0)->comment('Время с которого доступна квестовая точка');
            $table->time('time_end')->default(0)->comment('Время до которого доступна квестовая точка');

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('local_promotion_id')->references('id')->on('promotions');
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
        Schema::dropIfExists('geo_positions');
    }
}
