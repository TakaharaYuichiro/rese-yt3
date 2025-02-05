<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCourseRequest extends FormRequest
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
            'name' => 'required|string|max:191',
            'price' => 'required|integer|min:0',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'メニュー名は必須入力項目です',
            'price.required' => '単価は必須入力項目です',
            'price.integer' => '単価は整数を入力してください',
            'price.min' => '単価は0以上の整数を入力してください',
        ];
    }
}
