<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBulkNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bulk_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->string('email_heading',500)->nullable();
            $table->string('subject',500)->nullable();
            $table->text('message_greeting')->nullable();
            $table->text('message_body')->nullable();
            $table->text('message_signature')->nullable();
            $table->text('member_ids')->nullable();
            $table->enum('send_via',array('Push Notification','Email','Email to subscribrs'));
            $table->tinyInteger('status', false)->default(0)->comment("0 => Not sent yet, 1 => Sent, 2 => Processing");
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
        Schema::dropIfExists('user_bulk_notifications');
    }
}
