<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Terminated</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #dc3545; margin-top: 0;">Notice: Your Lease Contract Has Been Terminated</h2>
    </div>
    
    <p>Hello {{ $user->firstName }} {{ $user->lastName }},</p>
    
    <p>We are writing to inform you that your lease contract has been terminated.</p>
    
    <div style="background-color: #EFEFEA; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="color: #7F9267; margin-top: 0;">Contract Details:</h3>
        <p><strong>Contract ID:</strong> {{ $contract->contractID }}</p>
        @if($contract->stall)
            <p><strong>Stall:</strong> {{ $contract->stall->stallNo }}</p>
            @if($contract->stall->marketplace)
                <p><strong>Marketplace:</strong> {{ $contract->stall->marketplace->marketplace }}</p>
            @endif
        @endif
        <p><strong>Start Date:</strong> {{ $contract->startDate ? $contract->startDate->format('M d, Y') : 'N/A' }}</p>
        <p><strong>End Date:</strong> {{ $contract->endDate ? $contract->endDate->format('M d, Y') : 'N/A' }}</p>
        @if($reason)
            <p><strong>Reason for Termination:</strong> {{ $reason }}</p>
        @endif
    </div>
    
    <p>If you have any questions or need further assistance, please contact our team.</p>
    
    <p>Warm regards,<br>
    The D & G Properties Team<br>
    <em>powered by LEASE – LeaseEase X StoreEdge</em></p>
</body>
</html>
