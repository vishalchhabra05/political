<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoliticalPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('political_parties', function (Blueprint $table) {
            $table->id();
            $table->string('party_name',100);
            $table->string('short_name',100)->nullable();
            $table->string('logo',100)->nullable();
            $table->string('party_slogan',100)->nullable();
            $table->tinyInteger('status', false)->default(1)->comment("0-Pending, 1-Active, 2-Inactive, 3-Deleted");
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
        Schema::dropIfExists('political_parties');
    }
}
