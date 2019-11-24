<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefferalsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('refferals_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_sender_id');
            $table->unsignedInteger('user_recipient_id');
            $table->boolean('activated')->default(false);

            $table->foreign('user_sender_id')->references('id')->on('users');
            $table->foreign('user_recipient_id')->references('id')->on('users');

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
        Schema::dropIfExists('refferals_histories');
    }
}
