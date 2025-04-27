<?php
// app/Mail/VerificationEmail.php
namespace App\Mail;

use App\Models\UserVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public UserVerification $verification;

    public function __construct(UserVerification $verification)
    {
        $this->verification = $verification;
    }

    public function build()
    {
        $verificationUrl = url("/email/verify/{$this->verification->verificationToken}");

        return $this->subject('Verify Your Email Address')
            ->view('emails.verify')
            ->with([
                'user' => $this->verification->user,
                'verificationUrl' => $verificationUrl,
                'expiresAt' => $this->verification->expiresAt,
            ]);
    }
}
