<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->nullable()->index();
            $table->tinyInteger('role_id', false)->index()->comment("1-Superadmin, 2-Admin, 3-Subadmin");
            $table->string('national_id', 100)->nullable()->index();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('full_name',100)->nullable();
            $table->integer('country_id')->nullable()->index();
            $table->integer('state_id')->nullable()->index();
            $table->integer('city_id')->nullable()->index();
            $table->string('email',100)->unique()->index();
            $table->integer('country_code_id')->nullable()->index();
            $table->string('phone_number',100)->nullable();
            $table->integer('alt_country_code_id')->nullable()->index();
            $table->string('alternate_phone_number',100)->nullable();
            $table->string('national_id_image',100)->nullable();
            $table->string('password',255);
            $table->string('email_verify_code',255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_verify_code',255)->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->tinyInteger('status', false)->default(0)->comment("0-Pending, 1-Active, 2-Inactive, 3-Deleted");
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
        Schema::dropIfExists('admin_users');
    }
}
