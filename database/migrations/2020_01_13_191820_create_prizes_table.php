<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description',1000)->default('');
            $table->string('image_url',1000);

            $table->integer('summary_activation_count')->default(0);
            $table->integer('current_activation_count')->default(0);

            $table->boolean('is_active')->default(0);

            $table->unsignedInteger("company_id" );

            if (env("DB_CONNECTION") == 'mysql') {
                $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('prizes');
    }
}
