<!DOCTYPE html>
<html>
<head>
    <title>Bills Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">Bills Report</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Bill ID</th>
                <th>Tenant</th>
                <th>Stall</th>
                <th>Marketplace</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Date Paid</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $bill->billID }}</td>
                <td>{{ $bill->contract && $bill->contract->user ? trim($bill->contract->user->firstName . ' ' . $bill->contract->user->lastName) : 'N/A' }}</td>
                <td>{{ $bill->stall ? strtoupper($bill->stall->stallNo) : 'N/A' }}</td>
                <td>{{ $bill->stall && $bill->stall->marketplace ? $bill->stall->marketplace->marketplace : 'N/A' }}</td>
                <td>₱{{ number_format($bill->amount, 2) }}</td>
                <td>{{ $bill->dueDate ? $bill->dueDate->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $bill->datePaid ? $bill->datePaid->format('M d, Y h:i A') : '—' }}</td>
                <td>{{ $bill->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>
