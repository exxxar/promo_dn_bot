<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('address',500)->default('');
            $table->string('description',5000)->default('');
            $table->string('phone')->default('');
            $table->string('email')->default('');
            $table->double('cashback')->default(5);//индивиудальное значние CashBack для каждой компании
            $table->string('bailee')->default('');//имя ответственного лица от предприятия
            $table->string('logo_url',1000)->default('');
            $table->integer('position')->default(0);
            $table->integer('lottery_start_price')->default(1000);
            $table->string('menu_url',1000)->nullable();
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
        Schema::dropIfExists('companies');
    }
}
