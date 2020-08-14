<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->nullable();
            $table->string('description', 5000)->default('')->nullable();
            $table->string('promo_image_url', 1000)->nullable();
            $table->string('handler')->nullable();
            $table->integer('user_can_activate_count')->default(1);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('activation_count')->default(0);
            $table->integer('current_activation_count')->default(0);
            $table->string('location_address')->default('')->nullable();
            $table->string('location_coords')->default('')->nullable();
            $table->boolean('immediately_activate')->default(false);
            $table->string('activation_text', 1000)->default('');
            $table->integer('refferal_bonus')->default(0)->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->integer('position')->default(0);
            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('company_id')->references('id')->on('companies');
                $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('promotions');
    }
}
