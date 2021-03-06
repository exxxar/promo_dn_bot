<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoQuestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_quests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->comment('Заголовок квеста');
            $table->string('description',1000)->default('')->comment('Описание квеста');
            $table->string('image_url',1000)->default('')->comment('Изображение к квесту');
            $table->boolean('is_active')->default(true)->comment('Доступность квеста');
            $table->unsignedInteger('promotion_id')->nullable()->comment('Получение бонус в виде акционной скидки');
            $table->unsignedInteger('company_id')->comment('Квесты стартуют от определенной компании');
            $table->integer('reward_bonus')->default(0)->comment('Бонус в виде баллов');
            $table->integer('position')->default(0)->comment('Позиция в выдаче');
            $table->dateTime('start_at')->comment('Дата начала квеста');
            $table->dateTime('end_at')->comment('Дата окончания квеста');
            $table->timestamps();

            if (env("DB_CONNECTION")=='mysql') {
                $table->foreign('promotion_id')->references('id')->on('promotions');
                $table->foreign('company_id')->references('id')->on('companies');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_quests');
    }
}
