<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KamusSingkatan extends Model
{
    protected $table = "kamus_singkatan";

    protected $fillable = [
        "singkatan",
        "kepanjangan",
    ];
}
