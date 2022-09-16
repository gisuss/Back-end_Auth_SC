<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class massiveRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $password;
    public $emailConfirmationUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $cedula, $token)
    {
        $this->username = $name;
        $this->password = $cedula;
        $this->emailConfirmationUrl = "http://localhost:4200/auth/confirm-email/".$token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->subject("Bienvenido")
                    ->markdown('mails.register',    ['username' => $this->username,
                                                    'pass' => $this->password,
                                                    'link' => $this->emailConfirmationUrl]);
    }
}
