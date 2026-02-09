<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact form submission</title>
</head>
<body style="font-family: Helvetica Neue, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0;">
    <div style="width: 100%; padding: 40px 0; background-color: #f4f6f8;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #333333;">New contact form message</h2>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                <strong>From:</strong> {{ $name }} &lt;{{ $email }}&gt;
            </p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                <strong>Message:</strong>
            </p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5; white-space: pre-wrap;">{{ $messageText }}</p>
            <p style="color: #999999; font-size: 14px; margin-top: 24px;">
                Sent from the landing page contact form – {{ config('app.name') }}
            </p>
        </div>
    </div>
</body>
</html>
