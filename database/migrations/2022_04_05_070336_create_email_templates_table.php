<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255);
            $table->string('email_template', 255);
            $table->string('subject', 500);
            $table->string('message_greeting', 100)->nullable();
            $table->text('message_body');
            $table->string('message_signature', 100)->nullable();
            $table->text('dynamic_fields')->nullable();
            $table->integer('last_updated_by')->nullable()->index();
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
        Schema::dropIfExists('email_templates');
    }
}
