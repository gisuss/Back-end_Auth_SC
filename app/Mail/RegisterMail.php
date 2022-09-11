<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $ci;
    public $emailConfirmationUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $cedula)
    {
        $this->username = $name;
        $this->ci = $cedula;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->subject("Bienvenido.")
                    ->markdown('mails.register', ['username' => $this->username, 'cedula' => $this->ci]);
    }
}
