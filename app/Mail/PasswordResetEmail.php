<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
use Queueable, SerializesModels;

public $otp;
public $ip;

public function __construct(string $otp, string $ip)
{
$this->otp = $otp;
$this->ip = $ip;
}

public function build()
{
return $this->subject('Password Reset OTP')
->view('emails.password-reset')
->with([
'otp' => $this->otp,
'ip' => $this->ip,
]);
}
}