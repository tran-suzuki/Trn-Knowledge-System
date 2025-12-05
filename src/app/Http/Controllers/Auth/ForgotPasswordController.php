<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\MtUser;

use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $email = $request->input('email');

        $user = MtUser::query()
            ->where('email', $email)
            ->activeForAuth()
            ->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'メールアドレスを正しく入力してください。',
            ]);
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'パスワード再設定用のメールを送信しました。メールボックスをご確認ください。'
            );
        }

        return back()->withErrors([
            'email' => __($status),
        ]);
    }
}
