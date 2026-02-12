<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">Activity Logs Report</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Action Type</th>
                <th>Entity</th>
                <th>Entity ID</th>
                <th>Description</th>
                <th>User</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->activityID }}</td>
                <td>{{ $log->actionType }}</td>
                <td>{{ ucfirst($log->entity) }}</td>
                <td>{{ $log->entityID }}</td>
                <td>{{ $log->description ?? '-' }}</td>
                <td>{{ $log->user ? trim($log->user->firstName . ' ' . $log->user->lastName) : 'Unknown' }}</td>
                <td>{{ $log->user ? $log->user->email : 'N/A' }}</td>
                <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>
