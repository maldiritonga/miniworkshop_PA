<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $expiresMinutes
    ) {
    }

    public function build(): self
    {
        return $this->subject('Kode Reset Password - '.$this->code)
            ->view('emails.password-reset-code');
    }
}
