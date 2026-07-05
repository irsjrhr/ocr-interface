<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageStat extends Model
{
    protected $fillable = [
        'id',
        'total_api_keys',
        'total_webhooks_received',
    ];
}
