<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $url = null, $icon = 'fa-bell')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Solo enviar por correo si hay un host SMTP configurado y no es localhost o por defecto
        $mailHost = config('mail.mailers.smtp.host');
        if ($mailHost && $mailHost !== '127.0.0.1' && $mailHost !== 'mailpit') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->title)
                    ->greeting('Hola, Administrador')
                    ->line($this->message)
                    ->action('Ver Detalles', $this->url ?? url('/admin'))
                    ->line('Este es un aviso automático de ByMex.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
