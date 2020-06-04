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
            $table->string('instagram')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('avatar_url',1000)->default('')->nullable();
            $table->string('address',500)->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->string('age')->nullable();
            $table->string('birthday')->nullable();
            $table->string('source')->default('');
            $table->string('telegram_chat_id')->unique();
            $table->integer('referrals_count')->default(0);
            $table->integer('referral_bonus_count')->default(0);
            $table->integer('cashback_bonus_count')->default(0);
            $table->double('network_cashback_bonus_count')->default(0);
            $table->integer('current_network_level')->default(0);
            $table->integer('network_friends_count')->default(0);
            $table->boolean('is_admin')->default(false);
            $table->boolean('activated')->default(false);
            $table->unsignedInteger("parent_id")->nullable();

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
