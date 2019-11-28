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
            $table->string('description', 1000)->default('')->nullable();
            $table->string('event_image_url', 1000)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->unsignedInteger('company_id')->nullable();

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('company_id')->references('id')->on('companies');
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
