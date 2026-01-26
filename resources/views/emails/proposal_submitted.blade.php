<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Proposal Requirements Submitted</title>
</head>
<body style="font-family: Helvetica Neue, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0;">
    <div style="width: 100%; padding: 40px 0; background-color: #f4f6f8; text-align: center;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: left;">
            <h2 style="color: #333333;">Hello {{ $user->firstName }},</h2>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                We've successfully received your business proposal requirements submitted through <strong style="color:#333333;">LEASE (LeaseEase X StoreEdge)</strong>.
            </p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Our team will review your submission shortly. Once it has been evaluated, you'll receive an update on the status of your application through your dashboard or via email.
            </p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                You can also log in to your account anytime to check the progress of your application.
            </p>
            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Thank you for taking the time to complete your proposal. We'll be in touch soon!
            </p>

            <br>

            <p style="color: #555555; font-size: 16px; line-height: 1.5;">
                Best regards,<br>
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

