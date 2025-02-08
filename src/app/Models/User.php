<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    // use Billable;   // Stripe用

    // 初期値を定義する
    protected $attributes = [
        'role' => 21,   // 一般ユーザー
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function scopeKeywordSearch($query, $keyword_expression)
    {
        if (!empty($keyword_expression)) {
            $expression_s = mb_convert_kana($keyword_expression, 's'); // 全角スペースを半角スペースに変換
            $keywords = explode(' ', $expression_s);
            
            foreach($keywords as $keyword){
                $query->where(function ($query) use($keyword) {
                    $query->Where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });   
            }
        }
    }

    public function scopeRoleSearch($query, $role)
    {
        if (!empty($role)) {
            $query->where('role', $role);
        }
    }

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }
}
