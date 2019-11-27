<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashbackHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('cashback_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('money_in_check');
	        $table->string('user_phone')->default("")->nullable();
	        $table->string('check_info',255)->default("");
            $table->integer('activated')->default(false);
            $table->unsignedInteger('employee_id');

            if (env("DB_CONNECTION")=='mysql')
                $table->foreign('employee_id')->references('id')->on('users');

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
        Schema::dropIfExists('cashback_histories');
    }
}
