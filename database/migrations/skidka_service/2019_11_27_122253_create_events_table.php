<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::disableForeignKeyConstraints();
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->nullable();
            $table->string('description', 5000)->default('')->nullable();
            $table->string('event_image_url', 1000)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
           // $table->boolean('is_active')->default(false);
            $table->boolean('need_info')->default(false);
            $table->boolean('need_qr')->default(false);

            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('promo_id')->nullable();

            $table->integer('position')->default(0);

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('company_id')->references('id')->on('companies');
                $table->foreign('promo_id')->references('id')->on('promotions');
            }

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
        Schema::dropIfExists('events');
    }
}
