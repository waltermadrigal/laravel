<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['user_id', 'device_code', 'public_key', 'ip_address', 'is_active', 'is_demo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
