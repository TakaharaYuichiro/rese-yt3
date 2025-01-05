<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditShopRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '店名は必須入力項目です',
        ];
    }
}
