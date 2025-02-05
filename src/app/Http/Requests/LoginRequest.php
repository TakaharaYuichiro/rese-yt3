<?php

// 以下のファイルをコピーして、messagesを付け加えただけ
// vendor/laravel/fortify/src/Http/Requests/LoginRequest.php

// 以下の記事を参考
// https://qiita.com/JonyTask/items/ce6c7d4ffef32980f994

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Fortify;

class LoginRequest extends FormRequest
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
            Fortify::username() => 'required|string|email:strict|max:191',
            'password' => 'required|string',
        ];
    }


    public function messages()
    {
      return [
        Fortify::username().'.required' => 'メールアドレスを入力してください',
        Fortify::username().'.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
        'password.required' => 'パスワードを入力してください',
      ];
    }
}
