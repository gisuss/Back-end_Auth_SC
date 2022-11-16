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
    public $password;
    public $name;
    public $subject;
    public $linkpage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $username, $cedula)
    {
        $this->name = $name;
        $this->username = $username;
        $this->password = $cedula;
        $this->subject = "Bienvenido ".$this->name.", al SGSC.";
        $this->linkpage = "https://serviciocomunitariofacyt.netlify.app/auth/login";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->subject($this->subject)
                    ->markdown('mails.register',    ['username' => $this->username,
                                                    'pass' => $this->password,
                                                    'name' => $this->name,
                                                    'link' => $this->linkpage]);
    }
}
