<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'content_index'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime:Y-m-d H:i:s',    
        'updated_at'        => 'datetime:Y-m-d H:i:s',    
    ];
    
    public function user(){    
        return $this->belongsTo(User::class);
    }

}


