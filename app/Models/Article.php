<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $guarded = []; // deshabilito protección de asignación masica

    // protected $fillable = ['title', 'content' ];

}
