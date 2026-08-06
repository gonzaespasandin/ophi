<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeVerificationNotification extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = rtrim(config('app.spa_url'), '/').'/confirmar-email/'.$this->token;

        return (new MailMessage)
            ->subject('Confirmá tu nuevo email | Ophi')
            ->greeting('Hola 👋')
            ->line('Recibimos una solicitud para cambiar el email de tu cuenta de Ophi. Para confirmarlo, ingresá al siguiente enlace:')
            ->action('Confirmar mi email', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Hasta que lo confirmes, seguís ingresando con tu email anterior.')
            ->salutation('¡Te saluda el equipo de Ophi!');
    }
}
