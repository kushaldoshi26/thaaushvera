<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromotionalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subject_line;
    public string $headline;
    public string $body;
    public ?string $coupon_code;
    public ?float $discount;
    public string $cta_text;
    public string $cta_url;

    public function __construct(array $data)
    {
        $this->subject_line = $data['subject'] ?? 'Exclusive Offer from AUSHVERA 🌿';
        $this->headline     = $data['headline'] ?? 'A Special Offer Just for You';
        $this->body         = $data['body'] ?? 'Discover our latest wellness products and exclusive deals.';
        $this->coupon_code  = $data['coupon_code'] ?? null;
        $this->discount     = $data['discount'] ?? null;
        $this->cta_text     = $data['cta_text'] ?? 'Shop Now';
        $this->cta_url      = $data['cta_url'] ?? config('app.url') . '/products';
    }

    public function build(): self
    {
        return $this->subject($this->subject_line)->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $couponBlock = '';
        if ($this->coupon_code) {
            $discountText = $this->discount ? ($this->discount . '% OFF') : 'Special Discount';
            $couponBlock = <<<HTML
          <div style="background:linear-gradient(135deg,rgba(184,150,76,0.1),rgba(184,150,76,0.05));border:2px dashed #B8964C;border-radius:12px;padding:24px;margin:24px 0;text-align:center;">
            <div style="color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;">Use Coupon Code</div>
            <div style="font-size:28px;font-weight:bold;letter-spacing:8px;color:#B8964C;font-family:monospace;">{$this->coupon_code}</div>
            <div style="color:#6b7280;font-size:13px;margin-top:8px;">to get <strong style="color:#1a2f45;">{$discountText}</strong></div>
          </div>
HTML;
        }

        $body = $this->body;
        $ctaText = $this->cta_text;
        $ctaUrl = $this->cta_url;
        $headline = $this->headline;
        $profileUrl = config('app.url') . '/profile';

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0ece4;font-family:'Georgia',serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0ece4;padding:40px 20px;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#1a2f45,#0d1f35);padding:48px 40px;text-align:center;">
          <div style="color:#B8964C;font-size:10px;letter-spacing:4px;text-transform:uppercase;margin-bottom:16px;">🌿 AUSHVERA — Wellness, Refined by Nature</div>
          <div style="color:#f7f4ee;font-size:28px;line-height:1.3;letter-spacing:0.5px;">{$headline}</div>
          <div style="width:50px;height:1px;background:rgba(184,150,76,0.5);margin:20px auto 0;"></div>
        </td>
      </tr>
      <!-- Body -->
      <tr>
        <td style="padding:40px;">
          <p style="color:#374151;font-size:16px;line-height:1.8;margin:0 0 24px;">{$body}</p>
          {$couponBlock}
          <div style="text-align:center;margin-top:32px;">
            <a href="{$ctaUrl}" style="display:inline-block;background:linear-gradient(135deg,#B8964C,#d4a85a);color:#0b1c2d;text-decoration:none;padding:16px 48px;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:1px;text-transform:uppercase;">{$ctaText}</a>
          </div>
        </td>
      </tr>
      <!-- Divider -->
      <tr>
        <td style="padding:0 40px;">
          <div style="border-top:1px solid #e5e0d8;"></div>
        </td>
      </tr>
      <!-- Products teaser -->
      <tr>
        <td style="padding:32px 40px;text-align:center;">
          <div style="color:#9ca3af;font-size:12px;text-transform:uppercase;letter-spacing:2px;margin-bottom:20px;">Why Aushvera?</div>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="text-align:center;padding:12px;">
                <div style="font-size:24px;margin-bottom:8px;">🌿</div>
                <div style="font-size:12px;color:#6b7280;">100% Natural</div>
              </td>
              <td style="text-align:center;padding:12px;">
                <div style="font-size:24px;margin-bottom:8px;">🧬</div>
                <div style="font-size:12px;color:#6b7280;">Ayurvedic Formula</div>
              </td>
              <td style="text-align:center;padding:12px;">
                <div style="font-size:24px;margin-bottom:8px;">✨</div>
                <div style="font-size:12px;color:#6b7280;">Premium Quality</div>
              </td>
              <td style="text-align:center;padding:12px;">
                <div style="font-size:24px;margin-bottom:8px;">📦</div>
                <div style="font-size:12px;color:#6b7280;">Fast Delivery</div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td style="background:#1a2f45;padding:28px 40px;text-align:center;">
          <p style="color:rgba(247,244,238,0.5);font-size:12px;margin:0 0 8px;">© 2025 AUSHVERA. All rights reserved.</p>
          <p style="color:rgba(247,244,238,0.3);font-size:11px;margin:0;">You received this email because you are registered with Aushvera.<br>
          <a href="{$profileUrl}" style="color:#B8964C;">Manage preferences</a></p>
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
