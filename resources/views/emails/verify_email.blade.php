<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to LEASE</title>
</head>
<body style="font-family: Helvetica Neue, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0;">
    <div style="width: 100%; padding: 40px 0; background-color: #f4f6f8; text-align: center;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: left;">
            <h2 style="color: #333333;">Hello {{ $user->firstName }},</h2>  

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Welcome to <strong style="color:#333333;">LEASE (LeaseEase X StoreEdge)</strong> – the official property management platform of <strong style="color:#333333;">D & G Properties</strong>.
            </p>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                To start using your account, please confirm your email address and activate your account by clicking the button below:
            </p>

            <p>
                <a href="{{ url('/verify-email/'.$token) }}" target="_blank" 
                   style="display:inline-block; padding:12px 25px; background-color:#7F9267; color:#ffffff; text-decoration:none; border-radius:5px; font-weight:bold;">
                    Activate My Account
                </a>
            </p>

            <p style="color: #ff4d4f; font-size: 14px; line-height: 1.5;">
                ⚠️ Please note: This activation link will expire in <strong>60 minutes</strong>.
            </p>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Once activated, you’ll have full access to your dashboard and features.
            </p>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                If you didn’t sign up for LEASE, you can safely ignore this email.
            </p>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                We’re excited to have you on board!
            </p>

            <br>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Warm regards,<br>
                <strong style="color:#333333;">The D & G Properties Team</strong>
            </p>
            <p style="color: #999999; font-size: 14px; font-style: italic;">
                (powered by LEASE – LeaseEase X StoreEdge)
            </p>
        </div>

        <p style="color: #999999; font-size: 12px; margin-top: 20px; text-align: center;">
            &copy; {{ date('Y') }} D & G Properties. All rights reserved.
        </p>
    </div>
</body>
</html>
