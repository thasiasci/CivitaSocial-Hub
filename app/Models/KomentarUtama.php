<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OpdChannel;

class KomentarUtama extends Model
{
    protected $table = 'komentar_utama';
    protected $primaryKey = 'id';
    public $incrementing = false ;
    

    protected $fillable = [
        'id','channelId','videoId','title','textOriginal','sentimen','textCleaned',
        'authorDisplayName','authorProfileImageUrl','authorChannelUrl',
        'likeCount','publishedAt','totalReplyCount',
    ];

    public function opdChannel()
    {
        return $this->belongsTo(opdChannel::class,'channelId','channel_id');
    }
}
