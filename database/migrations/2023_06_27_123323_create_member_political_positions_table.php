<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberPoliticalPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_political_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('member_id')->index();
            $table->integer('political_position_id')->index();
            $table->integer('position_given_by')->index();
            $table->integer('position_given_by_role')->index();
            $table->text('country_id');
            $table->text('state_id')->nullable();
            $table->text('district_id')->nullable();
            $table->text('city_id')->nullable();
            $table->text('municipal_district_id')->nullable();
            $table->text('town_id')->nullable();
            $table->text('place_id')->nullable();
            $table->text('neighbourhood_id')->nullable();
            $table->text('recintos_id')->nullable();
            $table->text('college_id')->nullable();
            $table->tinyInteger('is_approved', false)->default(0)->comment("0 => Pending, 1 => Approved, 2 => Rejected");
            $table->integer('approved_by')->index()->nullable();
            $table->integer('approved_by_role')->index()->nullable();
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
        Schema::dropIfExists('member_political_positions');
    }
}
