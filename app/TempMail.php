<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempMail extends Model
{
    protected $fillable = [
        'email',
        'user_id',
        'ip_address',
        'last_checked_at',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_checked_at' => 'datetime'
    ];

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}