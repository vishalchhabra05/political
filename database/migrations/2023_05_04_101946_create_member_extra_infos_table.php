<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberExtraInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_extra_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->index();
            $table->integer('form_field_id')->index();
            $table->integer('form_field_option_id')->nullable()->index();
            $table->text('value')->nullable();
            $table->integer('member_work_info_id')->index()->nullable();
            $table->integer('member_educational_info_id')->index()->nullable();
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
        Schema::dropIfExists('member_extra_infos');
    }
}
