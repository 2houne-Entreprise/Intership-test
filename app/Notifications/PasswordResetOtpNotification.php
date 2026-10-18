<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Code de réinitialisation du mot de passe TaskFlow')
            ->greeting('Vérifiez votre adresse e-mail')
            ->line('Votre code de vérification TaskFlow :')
            ->line($this->code)
            ->line('Ce code est valable pendant 10 minutes et ne peut être utilisé qu’une seule fois.')
            ->line('Si vous n’avez pas demandé cette réinitialisation, ignorez ce message.');
    }
}
