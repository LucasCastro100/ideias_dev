<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public Company $company,
        public TeamInvitation $invitation,
        public string $inviterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('clientes.companies.accept-invitation', $this->invitation);

        return (new MailMessage)
            ->subject("Convite - {$this->company->name}")
            ->greeting("Olá!")
            ->line("{$this->inviterName} convidou você para fazer parte da empresa {$this->company->name}.")
            ->action('Aceitar Convite', $acceptUrl)
            ->line('Se você não esperava por este convite, ignore este email.');
    }
}
