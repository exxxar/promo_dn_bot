<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('charity_id');
            $table->unsignedInteger('company_id');
            $table->integer('donated_money')->default(0);

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('charity_id')->references('id')->on('charities');
                $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('charity_histories');
    }
}
