<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use RuntimeException;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $spaUrl = config('app.spa_url');

        if (blank($spaUrl)) {
            throw new RuntimeException('SPA_URL is not configured; cannot build the password reset link.');
        }

        $url = rtrim($spaUrl, '/') . '/reset-password/' . $this->token . '/' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Recuperar contraseña | Ophi')
            ->greeting('Hola 👋')
            ->line('Recibimos una solicitud para restablecer tu contraseña en Ophi. Podés hacerlo ingresando al siguiente enlace:')
            ->action('Reiniciar contraseña', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, podés ignorar este correo.')
            ->salutation('¡Te saluda el equipo de Ophi!');
    }
}