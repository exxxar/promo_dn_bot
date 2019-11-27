<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('fio_from_telegram')->default('');
            $table->string('fio_from_request')->default('')->nullable();

            $table->string('phone')->unique()->nullable();

            $table->string('avatar_url',1000)->default('')->nullable();
            $table->string('address',500)->default('')->nullable();
            $table->tinyInteger('sex')->default(1)->nullable();
            $table->smallInteger('age')->default(18)->nullable();
            $table->string('birthday')->default("")->nullable();


            $table->string('source')->default('');

            $table->string('telegram_chat_id')->unique();

            $table->integer('referrals_count')->default(0);

            $table->integer('referral_bonus_count')->default(0);
            $table->integer('cashback_bonus_count')->default(0);

            $table->boolean('is_admin')->default(false);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
