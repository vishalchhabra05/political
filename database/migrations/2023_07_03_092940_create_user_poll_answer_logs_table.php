<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPollAnswerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_poll_answer_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('poll_id')->index();
            $table->integer('user_poll_answer_id')->index();
            $table->integer('poll_option_id')->index();
            $table->integer('updated_by')->index()->nullable();
            $table->integer('updated_by_role')->index()->nullable();
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
        Schema::dropIfExists('user_poll_answer_logs');
    }
}
