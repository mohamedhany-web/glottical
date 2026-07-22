<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * عرض نموذج طلب رابط إعادة تعيين كلمة المرور
     */
    public function showLinkRequestForm()
    {
        $authBackgroundUrl = \App\Providers\AppServiceProvider::authBackgroundUrl();
        return view('auth.forgot-password', compact('authBackgroundUrl'));
    }

    /**
     * إرسال رابط إعادة التعيين إلى البريد المسجل (مع التحقق من وجود البريد)
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');
        // التأكد من أن البريد مسجل في الأكاديمية
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if (!$user) {
            return back()->withErrors(['email' => __('auth.email_not_registered')])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => __('auth.account_inactive')])->withInput();
        }

        $status = Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __('auth.reset_link_sent'));
        }

        return back()->withErrors(['email' => __('auth.reset_link_failed')])->withInput();
    }
}
