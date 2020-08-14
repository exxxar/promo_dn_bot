<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_hubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bot_url',191)->unique();
            $table->string('token_prod')->default('');
            $table->string('token_dev')->default('');
            $table->string('description')->default('');
            $table->string('bot_pic',1000)->default('');
            $table->string('webhook_url',1000)->default('');
            $table->boolean('is_active')->default(false);
            $table->double('money')->default(0);
            $table->double('money_per_day')->default(0);
            $table->unsignedInteger('company_id');
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
        Schema::dropIfExists('bot_hubs');
    }
}
