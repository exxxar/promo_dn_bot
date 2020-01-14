<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('promocodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->boolean('activated')->default(false);

            $table->boolean('prize_has_taken')->default(false);

            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('prize_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();


            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('company_id')->references('id')->on('companies');
                $table->foreign('prize_id')->references('id')->on('prizes');
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
        Schema::dropIfExists('promocodes');
    }
}
