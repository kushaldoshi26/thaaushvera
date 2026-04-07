<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f7f4ee;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #B8964C;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0B1C2D;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
        }
        .content-section {
            margin-bottom: 25px;
        }
        .label {
            font-weight: bold;
            color: #0B1C2D;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .value {
            background: #f7f4ee;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #B8964C;
            margin-bottom: 15px;
        }
        .message-content {
            background: #f7f4ee;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #B8964C;
            white-space: pre-wrap;
            line-height: 1.7;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .highlight {
            background: #B8964C;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AUSHVERA</div>
            <div class="subtitle">New Contact Form Submission</div>
        </div>

        <div class="content-section">
            <div class="label">From</div>
            <div class="value">
                <strong>{{ $name }}</strong><br>
                <span style="color: #666;">{{ $email }}</span>
            </div>
        </div>

        <div class="content-section">
            <div class="label">Message</div>
            <div class="message-content">
                {{ $message }}
            </div>
        </div>

        <div class="content-section">
            <div class="label">Submitted</div>
            <div class="value">
                {{ $submittedAt }}
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from the Aushvera contact form.</p>
            <p><span class="highlight">Reply to this email</span> to respond directly to the customer.</p>
        </div>
    </div>
</body>
</html>