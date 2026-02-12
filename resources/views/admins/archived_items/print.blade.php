<!DOCTYPE html>
<html>
<head>
    <title>Archived Items Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h4 class="mb-3">Archived Items Report</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Reference ID</th>
                <th>Archived At</th>
                <th>Archived From</th>
                <th>Archived By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($archivedItems as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['reference_id'] }}</td>
                <td>{{ $item['archived_at'] ?? '—' }}</td>
                <td>{{ $item['archived_from'] }}</td>
                <td>{{ $item['archived_by'] ?? '—' }}</td>
                <td>{{ $item['action'] ?? 'Archived' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.print();</script>
</body>
</html>
