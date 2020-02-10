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
            $table->integer('promo_bonus')->default(0);
            $table->integer('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('company_id')->nullable();

            if (env("DB_CONNECTION") == 'mysql') {
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
        Schema::dropIfExists('insta_promotions');
    }
}
