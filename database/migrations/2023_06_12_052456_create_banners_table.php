<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('PPID')->index();
            $table->text('content_text')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('updated_by')->index()->nullable();
            $table->integer('updated_by_role')->index()->nullable();
            $table->tinyInteger('status', false)->default(1)->comment("0-Inactive, 1-Active, 2-Deleted");
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
        Schema::dropIfExists('banners');
    }
}
