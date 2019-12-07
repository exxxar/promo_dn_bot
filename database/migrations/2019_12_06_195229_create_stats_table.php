<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stat_type')->nullable(false);
            $table->integer('stat_value')->nullable(false);

            $table->unsignedInteger('user_id')->nullable(false);

            if (env("DB_CONNECTION")=='mysql') {
                $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('stats');
    }
}
