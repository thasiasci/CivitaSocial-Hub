<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KamusEjaan extends Model
{
    protected $table = "kamus_ejaan";

    protected $fillable = [
        "ejaan_salah",
        "ejaan_benar",
    ];
}
