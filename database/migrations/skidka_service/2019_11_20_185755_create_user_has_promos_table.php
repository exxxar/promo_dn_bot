<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasPromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('user_has_promos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id");

            $table->integer('user_activation_count')->default(1);

            $table->unsignedInteger("promotion_id");

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('promotion_id')->references('id')->on('promotions');
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
        Schema::dropIfExists('user_has_promos');
    }
}
