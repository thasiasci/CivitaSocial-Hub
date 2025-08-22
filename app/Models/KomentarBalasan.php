<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\opdChannel;
use App\Models\KomentarUtama;

class KomentarBalasan extends Model
{
    protected $table = 'komentar_balasan';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id','parent_comment_id','channelId','videoId','title','textOriginal','sentimen',
        'textCleaned','authorDisplayName','authorProfileImageUrl','authorChannelUrl',
        'likeCount','publishedAt','totalReplyCount',
    ];

    public function opdChannel()
    {
        return $this->belongsTo(opdChannel::class,'channelId','channel_id');
    }

    public function parentComment()
    {
        return $this->belongsTo(KomentarUtama::class,'parent_comment_id','id');
    }
}
