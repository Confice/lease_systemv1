<!DOCTYPE html>
<html>
<head>
    <title>Stalls PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">Stall List</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Stall Name</th>
                <th>Marketplace</th>
                <th>Store</th>
                <th>Rent By</th>
                <th>Contract</th>
                <th>Status</th>
                <th>Size (sq. m.)</th>
                <th>Monthly Rental Fee</th>
                <th>Application Deadline</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stalls as $s)
            @php
                $contract = $s->contracts()->where('contractStatus', 'Active')->first();
                $rentBy = $contract && $contract->user ? $contract->user->firstName . ' ' . $contract->user->lastName : '-';
            @endphp
            <tr>
                <td>{{ $s->formatted_stall_id }}</td>
                <td>{{ $s->stallNo }}</td>
                <td>{{ $s->marketplace ? $s->marketplace->marketplace : '-' }}</td>
                <td>{{ $s->store ? $s->store->storeName : '-' }}</td>
                <td>{{ $rentBy }}</td>
                <td>{{ $contract ? $contract->contractStatus : '-' }}</td>
                <td>{{ $s->stallStatus }}</td>
                <td>{{ $s->size ?? '-' }}</td>
                <td>{{ number_format($s->rentalFee, 2) }}</td>
                <td>{{ optional($s->applicationDeadline)->format('Y-m-d') }}</td>
                <td>{{ optional($s->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
