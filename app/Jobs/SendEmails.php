<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $username;
    public $password;
    public $mail;
    public $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name, $username, $cedula, $email)
    {
        $this->username = $username;
        $this->password = $cedula;
        $this->mail = $email;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->mail)->queue(new RegisterMail($this->name, $this->username, $this->password));
    }
}
