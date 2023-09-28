<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('PPID')->index();
            $table->integer('form_id')->index();
            $table->enum('tab_type', ['personal_info', 'electoral_logistic', 'work_info', 'educational_info'])->nullable()->comment("In profile type form this tab_type is required");
            $table->string('field_name',100);
            $table->string('es_field_name',100);
            $table->enum('field_type', ['text', 'number', 'date', 'dropdown', 'checkbox', 'textarea', 'radio', 'file_upload']);
            $table->string('field_min_length',5000)->nullable();
            $table->string('field_max_length',5000)->nullable();
            $table->integer('decimal_points')->nullable();
            $table->tinyInteger('is_required', false)->default(0)->comment("0 => Not Required, 1 => Required");
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
        Schema::dropIfExists('form_fields');
    }
}
