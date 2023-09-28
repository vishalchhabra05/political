<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->string('survey_name',100)->nullable();
            $table->enum('survey_type', ['public', 'private']);
            $table->integer('form_id')->index();
            $table->date('end_date')->nullable();
            $table->tinyInteger('status', false)->default(0)->comment("0-Inactive, 1-Active, 2-Deleted");
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
        Schema::dropIfExists('surveys');
    }
}
