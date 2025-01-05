<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 
        'booked_datetime', 
        'booked_minutes', 
        'people_counts', 
        'user_id',
        'remarks',
        'charge_id',
    ];

    public function shop()
    {    
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {    
        return $this->belongsTo(User::class);
    }

    public function scopeKeywordSearch($query, $keyword_expression)
    {
        if (!empty($keyword_expression)) {
            $expression_s = mb_convert_kana($keyword_expression, 's'); // 全角スペースを半角スペースに変換
            $keywords = explode(' ', $expression_s);

            foreach($keywords as $keyword){
                $query->where(function ($query) use($keyword) {
                    $query->whereHas('user', function($subquery) use($keyword) {
                        $subquery->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                });
            }        
        }
    }

    public function scopeDateSearch($query, $date)
    {
        if (!empty($date)) {
            $query->whereDate('booked_datetime', $date);
        }
    }

    public function createQrCode($reservation) 
    {
        $datetimeStr = "";
        if (gettype($reservation['booked_datetime']) == "string") {
            $datetimeStr = $reservation['booked_datetime'];
        } else {
            $reservation['booked_datetime']->format('Y-m-d H:i');
        }

        $qrCodeContent = [
            'reservation_id' => $reservation -> id,
            'user_name' => $reservation['user']['name'],
            'shop_name' => $reservation['shop']['name'],
            'datetime' => $datetimeStr,
            'peple_count' => $reservation['people_counts']
        ];
        $jsonQrCodeContent = json_encode($qrCodeContent);
        return QrCode::size(300)->generate($jsonQrCodeContent);
    }

}
