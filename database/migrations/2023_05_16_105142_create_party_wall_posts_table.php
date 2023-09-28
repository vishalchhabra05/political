<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartyWallPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('party_wall_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('posted_by_member_id')->index()->nullable()->comment("Id of member");
            $table->integer('posted_by_admin_id')->index()->nullable()->comment("Id of admin user");
            $table->enum('post_type', ['News', 'Partywall','Post']);
            $table->string('post_heading',500)->nullable();
            $table->string('post_image',255)->nullable();
            $table->string('post_video',255)->nullable();
            $table->string('post_description',2000)->nullable();
            $table->datetime('posted_date_time');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->tinyInteger('is_approved', false)->default(0)->comment("0 => Pending, 1 => Approved, 2 => Rejected");
            $table->integer('approved_by')->index()->nullable();
            $table->integer('approved_by_role')->index()->nullable();
            $table->integer('category_id')->index()->nullable();
            $table->tinyInteger('status', false)->default(0)->comment("0 => Inactive, 1 => Active, 2 => Deleted");
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
        Schema::dropIfExists('party_wall_posts');
    }
}
