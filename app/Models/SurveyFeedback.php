<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyFeedback extends Model
{
    protected $table = 'survey_feedbacks';
    public $timestamps = true;
    protected $fillable = [
        'survey_id', 'PPID', 'member_id', 'survey_demographic_id', 'form_id', 'form_field_id', 'form_field_option_id', 'value'
    ];
}
