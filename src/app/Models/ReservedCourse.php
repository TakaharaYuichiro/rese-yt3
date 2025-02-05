<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservedCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id', 
        'course_id', 
        'price_as_of_reservation', 
    ];

    public function reservation(){    
        return $this->belongsTo(Reservation::class);
    }
    public function course(){    
        return $this->belongsTo(Course::class);
    }
}
