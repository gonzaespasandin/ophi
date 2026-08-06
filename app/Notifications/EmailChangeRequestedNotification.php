<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangeRequestedNotification extends Notification
{
    public function __construct(public string $newEmail) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Se solicitó un cambio de email | Ophi')
            ->greeting('Hola 👋')
            ->line('Alguien solicitó cambiar el email de tu cuenta de Ophi a '.$this->newEmail.'.')
            ->line('Si fuiste vos, revisá esa casilla para confirmar el cambio.')
            ->line('Si no fuiste vos, cambiá tu contraseña cuanto antes: alguien podría tener acceso a tu cuenta.')
            ->salutation('¡Te saluda el equipo de Ophi!');
    }
}
