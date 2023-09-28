<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('image', 500);
            $table->string('link_url', 500)->nullable();
            $table->integer('added_by_user')->nullable()->index();
            $table->integer('updated_by_user')->nullable()->index();
            $table->date('end_date')->nullable();
            $table->tinyInteger('status',false)->default(1)->comment('1-Active, 2-Deleted');
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
        Schema::dropIfExists('advertisements');
    }
}
