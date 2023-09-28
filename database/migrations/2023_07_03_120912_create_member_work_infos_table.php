<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberWorkInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_work_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('member_id')->index();
            $table->enum('work_status', ['Employee','Unemployee','Independent']);
            $table->enum('job_type', ['Private','Public']);
            $table->string('company_name')->nullable();
            $table->integer('job_title_id')->nullable();
            $table->string('company_phone')->nullable();
            $table->integer('country_code_id')->nullable()->index();
            $table->integer('company_industry_id')->index();
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
        Schema::dropIfExists('member_work_infos');
    }
}
