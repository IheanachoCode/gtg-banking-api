<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpNotification extends Notification
{
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
            ->subject('Reset Password')
            ->line('Your One Time Reset Password No is: ' . $this->otp)
            ->line('If you did not request this, please ignore this email.');
    }
}