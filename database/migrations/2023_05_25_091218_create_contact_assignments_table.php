<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('PPID')->index();
            $table->integer('contact_member_id')->index();
            $table->integer('member_id')->index();
            $table->integer('added_by')->index();
            $table->tinyInteger('status', false)->default(1)->comment("0 => Inactive, 1 => Active, 2 => Deleted");
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
        Schema::dropIfExists('contact_assignments');
    }
}
