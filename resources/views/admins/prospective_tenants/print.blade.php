<!DOCTYPE html>
<html>
<head>
    <title>Applications - {{ $stall->stallNo ?? 'Stall' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">Applications for Stall {{ strtoupper($stall->stallNo ?? '') }} ({{ $stall->marketplace ? $stall->marketplace->marketplace : 'N/A' }})</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Application ID</th>
                <th>Tenant</th>
                <th>Email</th>
                <th>Applied At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $app->applicationID }}</td>
                <td>{{ $app->user ? trim($app->user->firstName . ' ' . $app->user->lastName) : 'N/A' }}</td>
                <td>{{ $app->user ? $app->user->email : 'N/A' }}</td>
                <td>{{ $app->created_at ? $app->created_at->format('M d, Y h:i A') : '—' }}</td>
                <td>{{ $app->appStatus }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>
