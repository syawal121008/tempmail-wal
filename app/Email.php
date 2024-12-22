<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'temp_mail_id',
        'message_id',
        'from',
        'from_name',
        'subject',
        'body',
        'has_attachments',
        'received_at'
    ];

    protected $casts = [
        'has_attachments' => 'boolean',
        'received_at' => 'datetime'
    ];

    public function tempMail()
    {
        return $this->belongsTo(TempMail::class);
    }
}