<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->integer('survey_id')->index();
            $table->integer('PPID')->index();
            $table->integer('member_id')->index();
            $table->integer('survey_demographic_id')->index();
            $table->integer('form_id')->index();
            $table->integer('form_field_id')->index();
            $table->integer('form_field_option_id')->nullable()->index();
            $table->text('value')->nullable();
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
        Schema::dropIfExists('survey_feedbacks');
    }
}
