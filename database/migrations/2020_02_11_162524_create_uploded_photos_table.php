<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUplodedPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploded_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');

            $table->boolean('activated')->default(false);

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('insta_promotions_id');


            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('insta_promotions_id')->references('id')->on('insta_promotions');
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
        Schema::dropIfExists('uploded_photos');
    }
}
