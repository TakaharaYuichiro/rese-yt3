<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Mail;                   
use App\Mail\ChangeRoleNotificationMail;
use App\Consts\CommonConst;

class ChangeRoleController extends Controller
{
    public function index(Request $request) 
    {
        $target_user = User::find($request->user_id);
        return view('admin/change_role', compact('target_user'));
    }

    public function updateRole(Request $request) {
        if (isset($_POST['cancel'])){
            return redirect('/admin');
        }

        $data = [
            'role' => intval($request->new_role), 
        ];
        $user = User::find($request->user_id);
        $user->update($data);
        $message = $user['name'].'さんの権限を'.CommonConst::ROLE[$request->new_role].'に変更しました。';

        if (isset($request->is_mail_sending)){
            // メール送信
            $mailContent = [
                'name' => $user['name'],
                'before' =>  $request['current_role'],
                'after' =>  $request['new_role'],
            ];
            Mail::to($user['email'])
                ->send(new ChangeRoleNotificationMail($mailContent));
            $message .= 'また、'.$user['name'].'さんにメールを送信しました。';
        }

        return redirect('/admin')->with('message', $message);
    }
}
