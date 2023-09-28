<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us_enquiries', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('email',100);
            $table->string('phone_number',55);
            $table->string('message',2000);
            $table->string('reply',2000)->nullable();
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
        Schema::dropIfExists('contact_us_enquiries');
    }
}
