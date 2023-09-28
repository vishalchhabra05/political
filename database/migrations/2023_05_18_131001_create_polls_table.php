<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->string('poll_name',255);
            $table->string('question',255);
            $table->enum('poll_type',array('election','opinion'));
            $table->integer('election_id')->index()->nullable();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->tinyInteger('status', false)->default(0)->comment("0 => Inactive, 1 => Active, 2 => Deleted");
            $table->tinyInteger('is_approved', false)->default(0)->comment("0 => Pending, 1 => Approved, 2 => Rejected");
            $table->integer('approved_by')->index()->nullable();
            $table->integer('created_by_admin_id')->index()->nullable();
            $table->integer('created_by_member_id')->index()->nullable();
            $table->integer('notification_id')->index()->nullable();
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
        Schema::dropIfExists('polls');
    }
}
