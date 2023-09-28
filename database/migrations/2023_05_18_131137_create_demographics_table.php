<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemographicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demographics', function (Blueprint $table) {
            $table->id();
            $table->integer('entity_id')->index()->nullable()->comment("It can be poll id, survey id");
            $table->enum('entity_type', ['Poll', 'Survey','member_electoral_infos','member_work_infos']);
            $table->text('country_id')->nullable();
            $table->text('state_id')->nullable();
            $table->text('district_id')->nullable();
            $table->text('city_id')->nullable();
            $table->text('town_id')->nullable();
            $table->text('municiple_district_id')->nullable();
            $table->text('place_id')->nullable();
            $table->text('neighbourhood_id')->nullable();
            $table->text('recintos_id')->nullable();
            $table->text('college_id')->nullable();
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
        Schema::dropIfExists('poll_demographics');
    }
}
