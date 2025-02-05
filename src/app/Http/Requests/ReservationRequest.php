<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'date|after:today',
            'quantity.*' => 'integer|min:0'
        ];
    }

    public function messages()
    {
        return [
            'date.after' => '予約の日付は明日以降を指定してください',
            'quantity.*.integer' => '予約のコースメニューの数量には整数を入力してください',
            'quantity.*.min' => '予約のコースメニューの数量は0以上の値を入力してください',
        ];
    }
}
