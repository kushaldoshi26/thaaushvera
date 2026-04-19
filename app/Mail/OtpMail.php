<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $purpose;

    public function __construct(string $otp, string $purpose = 'verification')
    {
        $this->otp     = $otp;
        $this->purpose = $purpose;
    }

    public function build(): self
    {
        $subject = match($this->purpose) {
            'registration' => 'Verify Your Aushvera Account — OTP',
            'reset'        => 'Password Reset OTP — AUSHVERA',
            'login'        => 'Your Login OTP — AUSHVERA',
            default        => 'Your OTP — AUSHVERA',
        };

        return $this
            ->subject($subject)
            ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $title = match($this->purpose) {
            'registration' => 'Verify Your Email',
            'reset'        => 'Reset Your Password',
            default        => 'Your One-Time Password',
        };

        $msg = match($this->purpose) {
            'registration' => 'Use the OTP below to verify your email address and activate your Aushvera account.',
            'reset'        => 'Use the OTP below to reset your password. This code expires in 15 minutes.',
            default        => 'Use the OTP below to complete your action. This code expires in 10 minutes.',
        };

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0ece4;font-family:'Georgia',serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0ece4;padding:40px 20px;">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#1a2f45,#0d1f35);padding:40px;text-align:center;">
          <div style="color:#B8964C;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin-bottom:12px;">AUSHVERA</div>
          <div style="color:#f7f4ee;font-size:24px;letter-spacing:1px;">{$title}</div>
          <div style="width:40px;height:2px;background:#B8964C;margin:16px auto 0;"></div>
        </td>
      </tr>
      <!-- Body -->
      <tr>
        <td style="padding:40px;text-align:center;">
          <p style="color:#374151;font-size:16px;line-height:1.6;margin:0 0 32px;">{$msg}</p>
          <!-- OTP Box -->
          <div style="background:linear-gradient(135deg,#1a2f45,#0d1f35);border-radius:12px;padding:28px;margin:0 auto 32px;max-width:280px;">
            <div style="letter-spacing:16px;font-size:40px;font-family:monospace;font-weight:bold;color:#B8964C;">{$this->otp}</div>
            <div style="color:rgba(247,244,238,0.5);font-size:12px;margin-top:12px;">One-Time Password</div>
          </div>
          <p style="color:#9ca3af;font-size:13px;line-height:1.6;">Do not share this OTP with anyone. AUSHVERA will never ask for your OTP.</p>
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td style="background:#f9f7f3;padding:24px;text-align:center;border-top:1px solid #e5e0d8;">
          <p style="color:#9ca3af;font-size:12px;margin:0;">© 2025 AUSHVERA. All rights reserved.<br>Wellness, Refined by Nature.</p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
