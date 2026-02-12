<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #198754; margin-top: 0;">Congratulations! Your Application Has Been Approved</h2>
    </div>
    
    <p>Hello {{ $user->firstName }} {{ $user->lastName }},</p>
    
    <p>We are pleased to inform you that your application has been approved. You have been successfully assigned to the stall.</p>
    
    <div style="background-color: #EFEFEA; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="color: #7F9267; margin-top: 0;">Lease Details:</h3>
        <p><strong>Application ID:</strong> {{ $application->applicationID }}</p>
        @if($stall)
            <p><strong>Stall:</strong> {{ $stall->stallNo }}</p>
            @if($stall->marketplace)
                <p><strong>Marketplace:</strong> {{ $stall->marketplace->marketplace }}</p>
            @endif
        @endif
        @if($contract)
            <p><strong>Contract ID:</strong> {{ $contract->contractID }}</p>
            <p><strong>Start Date:</strong> {{ $contract->startDate ? $contract->startDate->format('M d, Y') : 'N/A' }}</p>
        @endif
    </div>
    
    <p>You can now access your lease and stall information through your tenant dashboard.</p>
    
    <p>If you have any questions, please do not hesitate to contact us.</p>
    
    <p>Warm regards,<br>
    The D & G Properties Team<br>
    <em>powered by LEASE – LeaseEase X StoreEdge</em></p>
</body>
</html>
