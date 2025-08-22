<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdChannel extends Model
{
    protected $table = 'opd_channels';
    protected $fillable =[
        'channel_id',
        'opd_name',
        'youtube_username',
    ];
}
