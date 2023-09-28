<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberElectoralInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_electoral_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('member_id')->index();
            $table->string('electoral_college')->nullable();
            $table->string('electoral_precint')->nullable();
            $table->string('electoral_town')->nullable();
            $table->string('electoral_precint_address')->nullable();
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
        Schema::dropIfExists('member_electoral_infos');
    }
}
