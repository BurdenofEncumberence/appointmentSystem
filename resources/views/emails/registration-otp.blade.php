<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYMNET Verification Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F1E9;
            color: #12150F;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #FCFBF7;
            border: 2px solid #12150F;
            border-radius: 16px;
            box-shadow: 4px 6px 0px #12150F;
            padding: 36px 32px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .brand-badge {
            background: #12150F;
            color: #E5A823;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            letter-spacing: 1px;
            display: inline-block;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 12px 0;
            color: #12150F;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #565A4E;
            margin: 0 0 20px 0;
        }
        .otp-box {
            background: #F4F1E9;
            border: 2px dashed #12150F;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin: 28px 0;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 38px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #12150F;
            margin: 0;
        }
        .otp-caption {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #565A4E;
            margin-top: 8px;
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #E4E0D4;
            font-size: 12px;
            color: #8C8F84;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <span class="brand-badge">KYMNET</span>
        </div>

        <h1>Verify your email address</h1>
        <p>Hi {{ $userName }},</p>
        <p>Thanks for joining KYMNET! To complete your court account registration, please enter the 6-digit verification code below:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
            <div class="otp-caption">Valid for {{ $expiresInMinutes }} minutes</div>
        </div>

        <p>If you did not request this registration, please ignore this email. No account will be created without this verification code.</p>

        <div class="footer">
            &copy; {{ date('Y') }} KYMNET &middot; Davao Pickleball Arena & Court Booking
        </div>
    </div>
</body>
</html>
