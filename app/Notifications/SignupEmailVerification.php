<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SignupEmailVerification extends Notification
{
    use Queueable;

    private $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Email')
            ->view('emails.signup-verification', [
                'otp' => $this->otp
            ]);
    }
}