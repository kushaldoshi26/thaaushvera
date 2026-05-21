<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $type; // 'registration' or 'login'

    public function __construct(User $user, string $type = 'login')
    {
        $this->user = $user;
        $this->type = $type;
    }

    public function build(): self
    {
        $subject = match($this->type) {
            'registration' => 'Welcome to Aushvera — Thank You for Registering',
            default        => 'Thank You for Choosing Aushvera — Security Alert / Logged In',
        };

        return $this
            ->subject($subject)
            ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $name = htmlspecialchars($this->user->name);
        if ($this->type === 'registration') {
            $title = "Welcome to Aushvera";
            $message = "We are absolutely thrilled to welcome you to the Aushvera family! Thank you so much for registering an account with us. We are dedicated to providing the most refined, pure, and natural Ayurvedic wellness experiences crafted specifically for your holistic lifestyle. Enjoy exploring our signature collection!";
            $cta = '<a href="' . url('/profile') . '" style="display:inline-block;background:linear-gradient(135deg,#B8964C,#9B7B36);color:#fff;text-decoration:none;padding:14px 28px;font-size:14px;border-radius:30px;letter-spacing:1px;text-transform:uppercase;">Explore Your Profile</a>';
        } else {
            $title = "Successful Login Alert";
            $message = "This is a quick confirmation to let you know that you have successfully logged into your Aushvera account recently. If this was you, no action is required—your session is perfectly secure. Thank you for your continued trust in Aushvera!";
            $cta = '<a href="' . url('/profile') . '" style="display:inline-block;background:linear-gradient(135deg,#1a2f45,#0d1f35);color:#fff;text-decoration:none;padding:14px 28px;font-size:14px;border-radius:30px;letter-spacing:1px;text-transform:uppercase;">View Account Activity</a>';
        }

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
          <p style="color:#1f2937;font-size:18px;font-weight:bold;margin:0 0 16px;text-align:left;">Dear {$name},</p>
          <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 32px;text-align:left;">{$message}</p>
          
          <div style="margin:36px 0;">
            {$cta}
          </div>
          
          <p style="color:#9ca3af;font-size:13px;line-height:1.6;">If you did not authorize this action or suspect any suspicious behavior, please secure your credentials and contact AUSHVERA Support immediately.</p>
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
