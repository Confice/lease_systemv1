<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentation Scheduled</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #7F9267; margin-top: 0;">Confirmation of Your Business Proposal Presentation</h2>
    </div>
    
    <p>Hello {{ $user->firstName }} {{ $user->lastName }},</p>
    
    <p>We are pleased to confirm that your business proposal presentation with D & G Properties has been scheduled.</p>
    
    <div style="background-color: #EFEFEA; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="color: #7F9267; margin-top: 0;">Appointment Details:</h3>
        <p><strong>Date:</strong> {{ $presentationDate }}</p>
        <p><strong>Time:</strong> {{ $presentationTime }}</p>
        <p><strong>Location:</strong> LOREM IPSUM FOR NOW</p>
    </div>
    
    <p>Please arrive on time and bring any materials needed to present your business proposal. Our team looks forward to meeting you in person and learning more about your business.</p>
    
    <p>If you need to reschedule or have any questions prior to your appointment, please contact us at {{ $marketplace->telephoneNo ?? ($marketplace->viberNo ?? 'N/A') }}{{ $marketplace->telephoneNo && $marketplace->viberNo ? ' or ' . $marketplace->viberNo : '' }}.</p>
    
    <p>We look forward to welcoming you!</p>
    
    <p>Warm regards,<br>
    The D & G Properties Team<br>
    <em>powered by LEASE – LeaseEase X StoreEdge</em></p>
</body>
</html>

