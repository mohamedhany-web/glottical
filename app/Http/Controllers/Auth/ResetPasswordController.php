<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    /**
     * عرض نموذج إعادة تعيين كلمة المرور (الرابط الوارد في البريد)
     */
    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email', old('email'));
        $authBackgroundUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            ? storage_asset(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            : asset('images/brainstorm-meeting.jpg');
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'authBackgroundUrl' => $authBackgroundUrl,
        ]);
    }

    /**
     * تحديث كلمة المرور بعد التحقق من الرابط
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
            'password.required' => __('auth.password_required'),
            'password.confirmed' => __('auth.password_confirmation_mismatch'),
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __('auth.password_reset_success'));
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }
}
