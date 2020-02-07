<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstaPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insta_promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('photo_url',1000)->nullable();
            $table->string('description',1000)->default('');
            $table->string('tag')->nullable();
            $table->integer('promo_bonus')->nullable();
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
        Schema::dropIfExists('insta_promotions');
    }
}
