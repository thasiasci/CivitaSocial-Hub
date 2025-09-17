<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramComment extends Model
{
    use HasFactory;
    
    protected $table = 'instagram_comments';
    
    protected $fillable = [
        'akun_kolaborasi',
        'link_konten', 
        'id_instagram',
        'periode', 
        'comment', 
        'sentimen', 
        'bulan',
        'is_spam'
    ];
}