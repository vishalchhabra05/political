<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    protected $table = 'cron_logs';
    public $timestamps = true;
    protected $fillable = [
        'cron_name','cron_start_time', 'cron_end_time'
    ];

}
