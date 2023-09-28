<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->index()->nullable();
            $table->integer('admin_user_id')->index()->nullable();
            $table->text('auth_token')->nullable();
            $table->timestamp('login_date_time')->nullable();
            $table->string('login_ip_address',100)->nullable();
            $table->string('login_location',100)->nullable();
            $table->timestamp('logout_date_time')->nullable();
            $table->string('logout_ip_address',100)->nullable();
            $table->string('logout_location',100)->nullable();
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
        Schema::dropIfExists('login_logs');
    }
}
