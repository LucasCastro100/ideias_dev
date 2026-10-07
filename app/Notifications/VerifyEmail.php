<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class VerifyEmail extends Notification
{
    protected function buildMailMessage(mixed $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirme seu email')
            ->greeting('Olá!')
            ->line('Clique no botão abaixo para verificar seu endereço de email.')
            ->action('Confirmar Email', $url)
            ->line('Se você não criou uma conta, ignore este email.');
    }
}
