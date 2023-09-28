<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('user_id')->index();
            $table->string('address',500)->nullable();
            $table->integer('country_id')->index();
            $table->integer('state_id')->index();
            $table->integer('city_id')->index();
            $table->integer('town_id')->index();
            $table->integer('municipal_district_id')->index();
            $table->integer('place_id')->index();
            $table->integer('neighbourhood_id')->index();
            $table->text('dob')->nullable();
            $table->text('age')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->integer('who_recommended')->index();
            $table->string('profile_image',500)->nullable();
            $table->tinyInteger('status', false)->default(0)->comment("0 => Draft: Member register but not completed profile, 1 => Pending : Completed profile , but not approved by admin, 2 => Approved : Approved by admin, 3 => Reject : Admin reject the Member");
            $table->tinyInteger('electoral_info_check', false)->default(0)->comment("0 =>Not filled, 1 =>Filled ");
            $table->tinyInteger('work_info_check', false)->default(0)->comment("0 =>Not filled, 1 =>Filled ");
            $table->tinyInteger('educational_info_check', false)->default(0)->comment("0 =>Not filled, 1 =>Filled ");
            $table->tinyInteger('is_approved', false)->default(0)->comment("0 => Pending, 1 => Approved, 2 => Rejected");
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
        Schema::dropIfExists('members');
    }
}
