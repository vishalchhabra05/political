<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EmailTemplate extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'email_templates';
    public $timestamps = true;
    
    protected $fillable = [
        'slug', 'email_template','subject','message_greeting','message_body','message_signature','dynamic_fields','last_updated_by'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User','last_updated_by');
    }
}
