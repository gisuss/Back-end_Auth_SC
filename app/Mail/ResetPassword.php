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
        // $this->ruta = url('/verify_token/'.$pin);
        $this->ruta = "http://localhost:4200/auth/change-password/".$pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->subject("Reset Password")
                    ->markdown('mails.password', ['ruta' => $this->ruta]);
    }
}
