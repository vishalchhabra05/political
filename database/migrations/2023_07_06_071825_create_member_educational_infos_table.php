<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberEducationalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_educational_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('member_id')->index();
            $table->integer('degree_level')->nullable();
            $table->string('bachelor_degree_id')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('stream')->nullable();
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
        Schema::dropIfExists('member_educational_infos');
    }
}
