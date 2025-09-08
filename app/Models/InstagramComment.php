<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramComment extends Model
{
    use HasFactory;
    protected $table = 'instagram_comments';
    protected $fillable = ['link_konten', 'periode', 'comment', 'sentimen', 'bulan'];
}