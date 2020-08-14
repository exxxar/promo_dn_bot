<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashBackInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_back_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->integer('value')->default(0);
            $table->integer('quest_bonus')->default(0);

            $table->dateTime('quest_begin_at')->nullable();
            $table->dateTime('quest_reset_at')->nullable();

            if (env("DB_CONNECTION")=='mysql') {
                $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('cash_back_infos');
    }
}
