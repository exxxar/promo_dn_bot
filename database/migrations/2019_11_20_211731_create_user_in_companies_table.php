<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::disableForeignKeyConstraints();
        Schema::create('user_in_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id" );
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger("company_id" );
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('user_in_companies');
    }
}
