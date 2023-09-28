<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPollAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_poll_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('PPID')->index();
            $table->integer('poll_id')->index();
            $table->integer('member_id')->index();
            $table->integer('poll_option_id')->index();
            $table->string('user_latitude')->nullable();
            $table->string('user_longitude')->nullable();
            $table->date('answer_date');
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
        Schema::dropIfExists('user_poll_answers');
    }
}
