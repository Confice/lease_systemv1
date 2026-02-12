<!DOCTYPE html>
<html>
<head>
    <title>My Leases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">My Leases</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Contract ID</th>
                <th>Stall</th>
                <th>Marketplace</th>
                <th>Monthly Rent</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days Remaining</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $contract->contractID }}</td>
                <td>{{ $contract->stall ? strtoupper($contract->stall->stallNo) : 'N/A' }}</td>
                <td>{{ $contract->stall && $contract->stall->marketplace ? $contract->stall->marketplace->marketplace : 'N/A' }}</td>
                <td>₱{{ $contract->stall ? number_format($contract->stall->rentalFee, 2) : '0.00' }}</td>
                <td>{{ $contract->startDate ? $contract->startDate->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $contract->endDate ? $contract->endDate->format('M d, Y') : 'No end date' }}</td>
                <td>{{ $contract->endDate ? now()->diffInDays($contract->endDate, false) . ' days' : '—' }}</td>
                <td>{{ $contract->contractStatus }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>
