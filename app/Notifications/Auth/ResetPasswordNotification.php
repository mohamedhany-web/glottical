<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        $expire = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject(__('auth.reset_password_email_subject'))
            ->greeting(__('auth.reset_password_email_greeting'))
            ->line(__('auth.reset_password_email_line'))
            ->action(__('auth.reset_password_email_action'), $url)
            ->line(__('auth.reset_password_email_expire', ['count' => $expire]))
            ->line(__('auth.reset_password_email_ignore'));
    }

    protected function resetUrl($notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
