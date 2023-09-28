<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->integer('ppid')->nullable();
            $table->string('national_id', 100)->nullable();
            $table->string('recommended_national_id', 100)->nullable();
            $table->string('full_name', 100)->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->string('email', 100)->index()->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->string('alternate_phone_number', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->integer('alt_country_code_id')->nullable();
            $table->integer('country_code_id')->nullable();
            $table->enum('login_type', ['Normal', 'Facebook', 'Google']);
            $table->string('google_id', 255)->nullable();
            $table->string('facebook_id', 255)->nullable();
            $table->string('linkedin_id', 255)->nullable();
            $table->tinyInteger('status', false)->default(1)->comment('0-Inactive, 1-Active, 2-Deleted');
            $table->tinyInteger('personal_info_check', false)->default(0)->comment('0 =>Not filled, 1 =>Filled  ');
            $table->string('email_verify_code', 255)->nullable();
            $table->string('email_verified_at', 255)->nullable();
            $table->string('phone_verify_code', 255)->nullable();
            $table->string('phone_verified_at', 255)->nullable();
            $table->enum('register_type', ['Militant', 'Sympathizer']);
            $table->integer('parent_user_id')->nullable();
            $table->integer('is_requested')->default(0)->comment('0=>Pending, 1=>Accepted, 2=>Cancelled, 3=>Auto Cancelled');
            $table->string('relationship_status', 255)->nullable();
            $table->string('recommended_relationship_status', 255)->nullable();
            $table->string('face_recognition_token', 255)->nullable();
            $table->string('fingerprint_recognition_token', 255)->nullable();
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
