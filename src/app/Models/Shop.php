<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Consts\CommonConst;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area_index', 'genre_id', 'content', 'image_filename'];

    public function genre(){    
        return $this->belongsTo(Genre::class);
    }

    public function evaluation(){
        return $this->hasMany(Evaluation::class);
    }

    public function getPrefName() {
        $prefCodes = CommonConst::PREF_CODE;
        return $prefCodes[$this->area_index];
    }

    public function scopeKeywordSearch($query, $keyword_expression){
        if (!empty($keyword_expression)) {
            $expression_s = mb_convert_kana($keyword_expression, 's'); // 全角スペースを半角スペースに変換
            $keywords = explode(' ', $expression_s);
            
            foreach($keywords as $keyword){
                $query->where(function ($query) use($keyword) {
                    $query->Where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('content', 'like', '%' . $keyword . '%');
                });   
            }
        }
    }

    public function scopeAreaSearch($query, $areaIndex){
        if (!empty($areaIndex)) {
            if ($areaIndex != "00"){
                $query->where('area_index', '=', $areaIndex);  
            }
        }
    }

    public function scopeGenreSearch($query, $genreId){
        if (!empty($genreId)) {
            if ($genreId != ''){
                $query->where('genre_id', '=', $genreId);  
            }
        }
    }
}
