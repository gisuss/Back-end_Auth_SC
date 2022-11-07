<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $ruta;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pin)
    {
        $this->ruta = "https://localhost:4200/auth/change-password/".$pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->subject("Reseteo de Contraseña")
                    ->markdown('mails.password', ['ruta' => $this->ruta]);
    }
}
