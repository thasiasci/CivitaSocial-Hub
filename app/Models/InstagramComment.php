<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramComment extends Model
{
    use HasFactory;
    protected $table = 'instagram_comments';
    protected $fillable = [
        'authorProfileUrl', 'authorProfileImageUrl', 'authorDisplayName',
        'commentUrl', 'publishedAt', 'comment', 'sentimen', 'likeCount', 'replyCount'
    ];
}