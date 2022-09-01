<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $reset_code;
    public function __construct($code)
    {
        $this->reset_code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('¡Hola!')
                    ->line('Ha sido recibida una solicitud de reseteo de contraseña asociada a esta dirección de correo electrónico')
                    ->line('Por favor, introduzca el siguiente código en tu página de reseteo de contraseña.')
                    ->line($this->reset_code)
                    ->line('Si tu no has solicitado un reseteo de contraseña, por favor haga caso omiso a este email.')
                    ->subject('Password reset request');
                    // ->action('Click to reset', $this->url)
                    // ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}