<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;
    protected $guarded = []; // deshabilito protección de asignación masica

    // protected $fillable = ['title', 'content' ];

    public function getRouteKeyName(){
        return 'slug';
    }

    public function imageUrl(){
        
        // return Storage::disk('public')->url($this->image);
        return $this->image ? 
            Storage::disk('local')->url($this->image) :
            'https://via.placeholder.com/640x480.png/6366f1/FFFFFF?text=no-image'
            ;

    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

}
