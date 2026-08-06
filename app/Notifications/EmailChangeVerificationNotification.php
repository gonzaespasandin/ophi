<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use RuntimeException;

class EmailChangeVerificationNotification extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $spaUrl = config('app.spa_url');

        if (blank($spaUrl)) {
            throw new RuntimeException('SPA_URL is not configured; cannot build the email confirmation link.');
        }

        $url = rtrim($spaUrl, '/').'/confirmar-email/'.$this->token;

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
