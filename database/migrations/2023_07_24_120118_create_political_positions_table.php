<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoliticalPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('political_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->string('political_position',100)->nullable();
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
        Schema::dropIfExists('political_positions');
    }
}
