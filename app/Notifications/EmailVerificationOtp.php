<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationOtp extends Notification
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
            ->subject('Verify Email')
            ->greeting('GTG')
            ->line('Please verify your email with OTP: ' . $this->otp)
            ->line('If you did not request this verification, please ignore this email.');
    }
}