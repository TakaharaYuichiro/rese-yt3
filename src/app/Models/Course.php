<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 
        'name', 
        'price', 
        'detail',
        'enable',
    ];

    public function shop(){    
        return $this->belongsTo(Shop::class);
    }
}
