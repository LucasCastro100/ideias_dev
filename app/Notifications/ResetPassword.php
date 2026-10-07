<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    protected function buildMailMessage(mixed $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Redefinir sua senha')
            ->greeting('Olá!')
            ->line('Você está recebendo este email porque recebemos uma solicitação de redefinição de senha para sua conta.')
            ->action('Redefinir Senha', $url)
            ->line('Este link de redefinição de senha expirará em :count minutos.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
            ->line('Se você não solicitou uma redefinição de senha, ignore este email.');
    }
}
